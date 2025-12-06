<?php

namespace App\Http\Controllers;

use App\Dao\Models\Category;
use App\Dao\Models\Core\User;
use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Services\Master\SingleService;
use App\Facades\Model\RaceModel;
use Plugins\Query;

class RaceController extends MasterController
{
    use CreateFunction, UpdateFunction;

    public function __construct(RaceModel $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    protected function beforeForm()
    {
        $user = Query::getUser();
        $jadwal = Query::getJadwal();

        self::$share = [
            'user' => $user,
            'jadwal' => $jadwal,
        ];
    }

    public function getUpdate($code)
    {
        $this->beforeForm();

        $model = $this->get($code, ['has_user']);
        $absen = $model->has_category ?? false;

        return moduleView(modulePathForm(path: self::$is_core), $this->share([
            'model' => $this->get($code),
            'absen' => $absen,
        ]));
    }

}
