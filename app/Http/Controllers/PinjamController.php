<?php

namespace App\Http\Controllers;

use App\Dao\Models\Asset;
use App\Http\Controllers\Core\MasterController;
use App\Services\Master\SingleService;
use App\Facades\Model\PinjamModel;
use App\Http\Requests\Core\GeneralRequest;
use App\Http\Requests\Core\PinjamRequest;
use App\Services\CreatePinjamService;
use App\Services\Master\CreateService;
use App\Services\UpdatePinjamService;
use Plugins\Query;
use Plugins\Response;

class PinjamController extends MasterController
{
    public function __construct(PinjamModel $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    protected function beforeForm()
    {
        $user = Query::getUser();
        $asset = Asset::getOptions();

        self::$share = [
            'user' => $user,
            'asset' => $asset,
        ];
    }

    public function postCreate(PinjamRequest $request, CreatePinjamService $service)
    {
        $data = $service->save($this->model, $request);
        return Response::redirectBack($data);
    }

    public function postUpdate($code, PinjamRequest $request, UpdatePinjamService $service)
    {
        $data = $service->update($this->model, $request, $code);

        return Response::redirectBack($data);
    }
}
