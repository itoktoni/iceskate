<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class PenggunaanModel extends \App\Dao\Models\Penggunaan
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}