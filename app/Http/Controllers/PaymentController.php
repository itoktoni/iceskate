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
use App\Services\UpdatePaymentService;
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

    public function postUpdate($code, PaymentRequest $request, UpdatePaymentService $service)
    {
        $data = $service->update($this->model, $request, $code);

        return Response::redirectBack($data);
    }

    public function getUpdate($code)
    {
        $model = Payment::where('payment_id', strval($code))->first();
        $jadwal = Voucher::where('payment_id', $code)->get();

        $this->beforeForm();

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
