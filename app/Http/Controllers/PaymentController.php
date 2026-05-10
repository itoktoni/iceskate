<?php

namespace App\Http\Controllers;

use App\Dao\Models\Absen;
use App\Dao\Models\Core\User;
use App\Dao\Models\Iuran;
use App\Dao\Models\Payment;
use App\Dao\Models\Voucher;
use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Http\Requests\Core\PaymentRequest;
use App\Services\Master\CreateService;
use App\Services\Master\SingleService;
use App\Services\UpdatePaymentService;
use Plugins\Response;

class PaymentController extends MasterController
{
    use CreateFunction, UpdateFunction;

    public function __construct(Payment $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    protected function beforeForm()
    {
        $user = User::getOptions();
        $iuran = Iuran::getOptions();

        self::$share = [
            'user' => $user,
            'iuran' => $iuran,
        ];
    }

    public function getTable()
    {
        $data = $this->model->dataRepository();
        return moduleView(modulePathTable(), [
            'data' => $data,
            'fields' => $this->model::getModel()->getShowField(),
        ]);
    }

    public function postCreate(PaymentRequest $request, CreateService $service)
    {
        $data = $service->save($this->model, $request);

        return Response::redirectBack($data);
    }

    public function postUpdate($code, PaymentRequest $request, UpdatePaymentService $service)
    {
        $data = $service->update($this->model, $request, $code);

        return Response::redirectBack($data);
    }

    public function getUpdate($code)
    {
        $model = Payment::where('payment_id', strval($code))->first();
        $jadwal = Voucher::where('payment_id', $code)->get();

        $this->beforeForm();

        return moduleView(modulePathForm(path: self::$is_core), $this->share([
            'model' => $model,
            'jadwal' => $jadwal
        ]));
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
