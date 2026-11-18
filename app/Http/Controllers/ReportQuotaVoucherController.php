<?php

namespace App\Http\Controllers;

use App\Dao\Models\Iuran;
use App\Dao\Models\Payment;
use App\Dao\Models\Voucher;
use App\Http\Controllers\Core\ReportController;
use Illuminate\Http\Request;
use Plugins\Query;

class ReportQuotaVoucherController extends ReportController
{
    public $data;

    public function __construct(Payment $model)
    {
        $this->model = $model::getModel();
    }

    protected function beforeForm()
    {
        self::$share = [
            'user' => Query::getUser(),
            'iuran' => Iuran::getOptions(),
        ];
    }

    public function getData()
    {
        $query = Voucher::query()->addSelect('*');

        if ($user = request()->get('user_id')) {
            $query = $query->where('payment_id_user', $user);
        }

        if ($iuran = request()->get('iuran_id')) {
            $query = $query->where('iuran_id', $iuran);
        }

        if ($start = request()->get('start_date')) {
            $query = $query->where(function ($q) use ($start) {
                $q->whereDate('use_date', '>=', $start)->orWhereNull('use_date');
            });
        }

        if ($end = request()->get('end_date')) {
            $query = $query->where(function ($q) use ($end) {
                $q->whereDate('use_date', '<=', $end)->orWhereNull('use_date');
            });
        }

        $rows = $query->orderBy('name', 'ASC')->orderBy('payment_id', 'ASC')->get();

        // Satu payment_id = satu voucher. Kelompokkan per user,
        // tiap voucher dihitung kuota / terpakai / sisa sendiri.
        $byUser = $rows->mapToGroups(fn ($item) => [$item->payment_id_user => $item]);

        $result = [];
        foreach ($byUser as $userId => $items) {
            $first = $items->first();

            $vouchers = $items->groupBy('payment_id')->map(function ($pakai) {
                $head = $pakai->first();
                $kuota = (int) ($head->payment_voucher ?? $pakai->count());
                $terpakai = $pakai->filter(fn ($i) => ! empty($i->use_date) || ! empty($i->jadwal_nama))->count();
                $sisa = max(0, $kuota - $terpakai);

                return [
                    'payment_id' => $head->payment_id,
                    'iuran_nama' => $head->iuran_nama,
                    'iuran_type' => $head->iuran_type ?? null,
                    'payment_tanggal' => $head->payment_tanggal ?? null,
                    'kuota' => $kuota,
                    'terpakai' => $terpakai,
                    'sisa' => $sisa,
                    'rincian' => $pakai->filter(fn ($i) => ! empty($i->use_date))->map(fn ($i) => [
                        'use_date' => $i->use_date,
                        'jadwal_nama' => trim(($i->jadwal_nama ?? '') . ' ' . ($i->jadwal_keterangan ?? '')),
                    ])->values(),
                ];
            })->values();

            $result[] = [
                'user_id' => $userId,
                'name' => $first->name ?? 'Atlet tidak ditemukan',
                'vouchers' => $vouchers,
                'kuota' => $vouchers->sum('kuota'),
                'terpakai' => $vouchers->sum('terpakai'),
                'sisa' => $vouchers->sum('sisa'),
            ];
        }

        usort($result, fn ($a, $b) => [$a['sisa'], $a['name']] <=> [$b['sisa'], $b['name']]);

        return [
            'habis' => array_values(array_filter($result, fn ($r) => $r['sisa'] <= 0)),
            'tersisa' => array_values(array_filter($result, fn ($r) => $r['sisa'] > 0)),
        ];
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $this->data = $this->getData($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
        ]));
    }
}
