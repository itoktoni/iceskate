<?php

namespace App\Http\Controllers;

use App\Dao\Models\Absen;
use App\Dao\Models\Payment;
use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Services\Master\SingleService;
use App\Facades\Model\AbsenModel;

class AbsenController extends MasterController
{
    use CreateFunction, UpdateFunction;

    public function __construct(AbsenModel $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    public function getTable()
    {
        $data = (new Payment())->dataRepository();
        return moduleView(modulePathTable(), [
            'data' => $data,
            'fields' => $this->model::getModel()->getShowField(),
        ]);
    }

    public function getUpdate($code)
    {
        $model = Payment::where('payment_code', strval($code))->first();
        Absen::where('jadwal_id', $model->jadwal_id)->where('id', $model->id)->update([
            'payment' => 1,
            'code' => date('Ymd').strtoupper(unic(5)),
            'payment_date' => date('Y-m-d')
        ]);

        return redirect()->back();
    }

    public function getDelete()
    {
        $code = request()->get('code');

        $model = $this->get($code);

        Absen::where('jadwal_id', $model->jadwal_id)->where('id', $model->id)->update([
            'payment' => null,
            'payment_date' => null,
            'code' => null
        ]);

        return redirect()->back();
    }
}
