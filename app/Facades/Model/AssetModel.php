<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class AssetModel extends \App\Dao\Models\Asset
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}