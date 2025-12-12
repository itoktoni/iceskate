<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class PinjamModel extends \App\Dao\Models\Pinjam
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}