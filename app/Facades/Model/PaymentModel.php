<?php

namespace App\Facades\Model;

use Illuminate\Support\Facades\Facade;

class PaymentModel extends \App\Dao\Models\Payment
{
    protected static function getFacadeAccessor()
    {
        return getClass(__CLASS__);
    }
}