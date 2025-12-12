<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class AbsenModel extends \App\Dao\Models\Absen
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}