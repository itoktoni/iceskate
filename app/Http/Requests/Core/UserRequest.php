<?php

namespace App\Http\Requests\Core;

use App\Dao\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UserRequest extends FormRequest
{
    use ValidationTrait;

    public function validation(): array
    {
        return [
            'name' => 'required|min:2',
            'email' => 'required|email',
            'password' => ['required', 'string', 'min:4', 'confirmed'],
        ];
    }
}
