<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class JarakModel extends \App\Dao\Models\Jarak
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}