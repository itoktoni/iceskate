<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Services\Master\SingleService;
use App\Facades\Model\PenggunaanModel;

class PenggunaanController extends MasterController
{
    use CreateFunction, UpdateFunction;

    public function __construct(PenggunaanModel $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    public static function field_name()
    {
        return 'payment_code';
    }

    public function getFieldNameAttribute()
    {
        return $this->{$this->field_name()};
    }

}
