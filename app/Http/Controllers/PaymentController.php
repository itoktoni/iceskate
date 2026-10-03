<?php

namespace App\Http\Controllers;

use App\Dao\Models\Absen;
use App\Dao\Models\Core\User;
use App\Dao\Models\Iuran;
use App\Dao\Models\Payment;
use App\Dao\Models\Voucher;
use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Http\Requests\Core\PaymentRequest;
use App\Services\Master\CreateService;
use App\Services\Master\SingleService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Plugins\Response;

class PaymentController extends MasterController
{
    use CreateFunction, UpdateFunction;

    public function __construct(Payment $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    protected function beforeForm()
    {
        $user = User::getOptions();
        $iuran = Iuran::getOptions();

        self::$share = [
            'user' => $user,
            'iuran' => $iuran,
            'status_bayar' => [
                1 => 'Sudah bayar (Paid)',
                0 => 'Belum bayar (Pending)',
            ],
            'method_options' => self::$settle_methods,
        ];
    }

    public function getTable()
    {
        $data = $this->model->dataRepository();
        return moduleView(modulePathTable(), [
            'data' => $data,
            'fields' => $this->model::getModel()->getShowField(),
        ]);
    }

    public function postCreate(PaymentRequest $request, CreateService $service)
    {
        $data = $service->save($this->model, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code)
    {
        $payment = Payment::where('payment_id', strval($code))->first();

        if (empty($payment)) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $validated = request()->validate([
            'payment_tanggal' => 'required|date',
            'payment_iuran'   => 'required',
            'payment_id_user' => 'required',
            'payment_paid'    => 'required|in:0,1',
            'payment_method'  => 'nullable|string|max:50',
            'payment_note'    => 'nullable|string|max:1000',
        ]);

        $paidNow    = (int) $validated['payment_paid'];
        $paidBefore = (int) $payment->payment_paid;
        $noteNow    = trim($validated['payment_note'] ?? '');
        $noteBefore = trim($payment->payment_note ?? '');

        // Status bayar dibalik tapi keterangan tidak diperbarui -> tolak, jejak audit wajib.
        if ($paidNow !== $paidBefore && $noteNow === $noteBefore) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ubah keterangan: jelaskan kenapa status bayar diubah (mis. ternyata belum terima uang / sudah bayar cash).');
        }

        $update = [
            'payment_tanggal' => $validated['payment_tanggal'],
            'payment_iuran'   => $validated['payment_iuran'],
            'payment_id_user' => $validated['payment_id_user'],
            'payment_paid'    => $paidNow,
        ];

        // Harga mengikuti iuran terpilih (perilaku lama).
        if ($validated['payment_iuran'] != $payment->payment_iuran) {
            $iuran = Iuran::find($validated['payment_iuran'], ['iuran_harga', 'iuran_voucher']);
            if ($iuran) {
                $update['payment_value']   = $iuran->iuran_harga;
                $update['payment_voucher'] = $iuran->iuran_voucher;
            }
        }

        if ($paidNow === 1 && $paidBefore === 0) {
            $update['payment_done']   = date('Y-m-d H:i:s');
            $update['payment_method'] = $validated['payment_method'] ?: 'MANUAL';
        } elseif ($paidNow === 0 && $paidBefore === 1) {
            $update['payment_done'] = null;
        } elseif (! empty($validated['payment_method'])) {
            $update['payment_method'] = $validated['payment_method'];
        }

        if ($noteNow !== $noteBefore || $paidNow !== $paidBefore) {
            $update['payment_note']      = $noteNow ?: null;
            $update['payment_settle_by'] = $this->settleBy();
        }

        $payment->update($update);

        Log::info('Edit payment via detail', [
            'order_id'    => $code,
            'paid_before' => $paidBefore,
            'paid_now'    => $paidNow,
            'admin_id'    => auth()->id(),
        ]);

        return redirect()->route(moduleAction('getTable'))->with('success', 'Pembayaran ' . $code . ' berhasil diperbarui.');
    }

    public function getUpdate($code)
    {
        $model = Payment::where('payment_id', strval($code))->first();
        $jadwal = Voucher::where('payment_id', $code)->get();

        $this->beforeForm();

        // Pertahankan metode gateway (mis. QRIS) agar tetap bisa dipilih.
        $methods = self::$settle_methods;
        if (! empty($model?->payment_method) && ! isset($methods[$model->payment_method])) {
            $methods = [$model->payment_method => $model->payment_method . ' (Cashi)'] + $methods;
        }
        self::$share['method_options'] = $methods;

        return moduleView(modulePathForm(path: self::$is_core), $this->share([
            'model' => $model,
            'jadwal' => $jadwal
        ]));
    }

    public static $settle_methods = [
        'CASH'     => 'CASH - Bayar tunai di kasir',
        'MANUAL'   => 'MANUAL - Koreksi / penyesuaian admin',
        'TRANSFER' => 'TRANSFER - Transfer bank manual',
    ];

    private function getPaymentDetail($code)
    {
        return $this->model->select($this->model->getTable() . '.*', 'name', 'iuran_nama')
            ->leftJoinRelationship('has_iuran')
            ->leftJoinRelationship('has_user')
            ->where($this->model->getTable() . '.payment_id', strval($code))
            ->first();
    }

    private function settleBy()
    {
        $user = auth()->user();

        return $user ? ($user->name ?? $user->email ?? ('#' . $user->id)) : 'system';
    }

    public function getSettle($code)
    {
        $payment = $this->getPaymentDetail($code);

        if (empty($payment)) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment->payment_paid) {
            return redirect()->back()->with('success', 'Pembayaran ' . $code . ' sudah lunas (' . ($payment->payment_method ?? '-') . ').');
        }

        return moduleView('pages.payment.settle', [
            'model'   => $payment,
            'mode'    => 'settle',
            'methods' => self::$settle_methods,
        ]);
    }

    public function postSettle($code)
    {
        $payment = Payment::where('payment_id', strval($code))->first();

        if (empty($payment)) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment->payment_paid) {
            return redirect()->back()->with('success', 'Pembayaran ' . $code . ' sudah lunas.');
        }

        $validated = request()->validate([
            'method' => 'required|in:CASH,MANUAL,TRANSFER',
            'note'   => 'required|string|min:5|max:1000',
        ], [
            'note.required' => 'Keterangan wajib diisi: kenapa pembayaran ini disettle manual (mis. bayar cash di kasir).',
            'note.min'      => 'Keterangan terlalu pendek, jelaskan kenapa manual.',
        ]);

        $payment->update([
            'payment_paid'      => 1,
            'payment_done'      => date('Y-m-d H:i:s'),
            'payment_method'    => $validated['method'],
            'payment_note'      => trim($validated['note']),
            'payment_settle_by' => $this->settleBy(),
        ]);

        Log::info('Manual settle payment', [
            'order_id'  => $code,
            'method'    => $validated['method'],
            'note'      => $validated['note'],
            'admin_id'  => auth()->id(),
        ]);

        return redirect()->route(moduleAction('getTable'))->with('success', 'Pembayaran ' . $code . ' dilunasi manual (' . $validated['method'] . ').');
    }

    public function getPending($code)
    {
        $payment = $this->getPaymentDetail($code);

        if (empty($payment)) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if (! $payment->payment_paid) {
            return redirect()->back()->with('success', 'Pembayaran ' . $code . ' masih pending.');
        }

        return moduleView('pages.payment.settle', [
            'model'   => $payment,
            'mode'    => 'pending',
            'methods' => self::$settle_methods,
        ]);
    }

    public function postPending($code)
    {
        $payment = Payment::where('payment_id', strval($code))->first();

        if (empty($payment)) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if (! $payment->payment_paid) {
            return redirect()->back()->with('success', 'Pembayaran ' . $code . ' masih pending.');
        }

        $validated = request()->validate([
            'note' => 'required|string|min:5|max:1000',
        ], [
            'note.required' => 'Alasan pembatalan wajib diisi.',
            'note.min'      => 'Alasan terlalu pendek, jelaskan kenapa dikembalikan ke pending.',
        ]);

        $prevMethod = $payment->payment_method;

        $payment->update([
            'payment_paid'      => 0,
            'payment_done'      => null,
            'payment_note'      => trim($validated['note']),
            'payment_settle_by' => $this->settleBy(),
        ]);

        Log::info('Manual pending payment', [
            'order_id'    => $code,
            'prev_method' => $prevMethod,
            'note'        => $validated['note'],
            'admin_id'    => auth()->id(),
        ]);

        return redirect()->route(moduleAction('getTable'))->with('success', 'Pembayaran ' . $code . ' dikembalikan ke pending.');
    }

    public function getCheck($code)
    {
        $payment = Payment::where('payment_id', strval($code))->first();

        if (empty($payment)) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        if ($payment->payment_paid) {
            return redirect()->back()->with('success', 'Pembayaran sudah lunas.');
        }

        $orderId = $payment->payment_code ?: $payment->payment_id;
        $baseUrl = rtrim(env('CASHI_BASE_URL', 'https://cashi.id'), '/');

        try {
            $response = Http::withHeaders(['x-api-key' => env('CASHI_KEY', '')])
                ->timeout(30)
                ->get($baseUrl . '/api/check-status/' . $orderId);

            $data = $response->json();

            if (! empty($data['success']) && ($data['status'] ?? '') === 'SETTLED') {
                $payment->update([
                    'payment_paid'   => 1,
                    'payment_done'   => date('Y-m-d H:i:s'),
                    'payment_method' => $data['payment_method'] ?? $data['channel'] ?? $payment->payment_method,
                ]);

                return redirect()->back()->with('success', 'Pembayaran ' . $code . ' lunas.');
            }

            return redirect()->back()->with('error', 'Belum dibayar (status: ' . ($data['status'] ?? 'unknown') . ').');
        } catch (\Throwable $e) {
            Log::error('Cashi check-status exception: ' . $e->getMessage(), ['order_id' => $orderId]);

            return redirect()->back()->with('error', 'Gagal cek ke Cashi, silakan coba lagi.');
        }
    }

    public function getDelete()
    {
        $code = request()->get('code');

        $model = $this->get($code);

        Absen::where('jadwal_id', $model->jadwal_id)->where('id', $model->id)->update([
            'payment' => null,
            'payment_date' => null,
            'code' => null
        ]);

        return redirect()->back();
    }
}
