<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class RaceModel extends \App\Dao\Models\Race
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}