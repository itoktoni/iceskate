<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class JadwalModel extends \App\Dao\Models\Jadwal
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}