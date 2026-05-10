<?php

namespace App\Http\Requests\Core;

use App\Dao\Models\Iuran;
use App\Dao\Traits\ValidationTrait;
use App\Facades\Model\MenuModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class PaymentRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            'payment_iuran' => 'required',
            'payment_tanggal' => 'required',
            'payment_id_user' => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $merge = [];

        $iuran = Iuran::find($this->payment_iuran, ['iuran_harga', 'iuran_voucher']);
        $harga = $iuran->iuran_harga;
        $token = $iuran->iuran_voucher;

        $code    = unic(10) . date('Ymd');
        $voucher    = unic(24);

        $merge = [
            'payment_id'      => $code,
            'payment_code'      => $voucher,
            'payment_tanggal' => $this->payment_tanggal,
            'payment_id_user' => $this->payment_id_user,
            'payment_value'   => $harga,
            'payment_paid'   => 1,
            'payment_done'   => date('Y-m-d H:i:s'),
            'payment_method'   => 'MANUAL',
            'payment_voucher'   => $token,
        ];

        $this->merge($merge);
    }
}
