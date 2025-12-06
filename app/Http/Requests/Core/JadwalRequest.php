<?php

namespace App\Http\Requests\Core;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;

class JadwalRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            'jadwal_category_id' => 'required',
            'jadwal_tanggal' => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $data = [];
        foreach (request('code', []) as $key) {

            $data[$key] = [
                'race_jadwal_id' => $this->jadwal_id,
                'race_category_id' => $this->jadwal_category_id,
                'race_tanggal' => $this->jadwal_tanggal,
                'race_user_id' => $key,
            ];
        }

        $this->merge([
            'race' => $data
        ]);
    }
}
