<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class IuranModel extends \App\Dao\Models\Iuran
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}