<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Services\Master\SingleService;
use App\Facades\Model\PaymentModel;

class PaymentController extends MasterController
{
    use CreateFunction, UpdateFunction;

    public function getData()
    {
        $query = $this->model->dataRepository()
            ;

        return $query;
    }

    public function __construct(PaymentModel $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    public function getUpdate($code)
    {
        $this->beforeForm();

        $model = $this->get($code, ['has_user']);
        $user = $model->has_user ?? false;
        $iuran = $model->has_iuran ?? false;

        return moduleView(modulePathForm(path: self::$is_core), $this->share([
            'model' => $this->get($code),
            'user' => $user,
            'iuran' => $iuran,
        ]));
    }
}
