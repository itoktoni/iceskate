<?php

namespace App\Http\Requests\Core;

use App\Dao\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class PinjamRequest extends FormRequest
{
    public function prepareForValidation()
    {
        $qty = $this->pinjam_qty ?? 0;

        if(!empty($this->pinjam))
        {
            if($this->pinjam >= $this->qty)
            {
                $qty = $this->pinjam - $this->qty;
            }
        }

        $this->merge([
            // 'content' => ''
            'pinjam_qty' => $qty
        ]);
    }

    public function withValidator($validator)
    {
        $available = Asset::find($this->pinjam_asset_id)->asset_qty;
        $request = $this->pinjam_qty;

        $qty = $available - $request;
        $alert = $salah = false;

        if($qty < 0){
            $alert = true;
        }

        // PINJAM
        if($this->pinjam != 0)
        {
            if($this->pinjam < $this->qty)
            {
                $salah = true;
            }
        }


        $validator->after(function ($validator) use ($alert, $available, $salah) {
            if($alert)
            {
                $validator->errors()->add('pinjam_qty', 'Qty tidak mencukupi, tersisa '.$available);
            }

            if($salah)
            {
                $validator->errors()->add('qty', 'Qty kembalikan lebih !');
            }

        });
    }

    public function rules()
    {
        return [
            'pinjam_asset_id' => 'required',
            'pinjam_user_id' => 'required',
            'pinjam_tanggal' => 'required',
        ];
    }
}
