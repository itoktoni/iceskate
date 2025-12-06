<?php

namespace App\Http\Controllers;

use App\Dao\Enums\Core\RoleType;
use App\Dao\Enums\JadwalType;
use App\Dao\Models\Category;
use App\Dao\Models\Core\User;
use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Services\Master\SingleService;
use App\Facades\Model\JadwalModel;
use App\Facades\Model\UserModel;
use App\Http\Requests\Core\GeneralRequest;
use App\Http\Requests\Core\JadwalRequest;
use App\Services\Core\UpdateJadwalService;
use Plugins\Query;
use Plugins\Response;

class JadwalController extends MasterController
{
    use CreateFunction, UpdateFunction;

    protected function beforeForm()
    {
        $user = Query::getUser();
        $jadwal = JadwalType::getOptions();
        $category = Category::getOptions();

        self::$share = [
            'user' => $user,
            'category' => $category,
            'jadwal' => $jadwal,
        ];
    }

    public function __construct(JadwalModel $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    public function getUpdate($code)
    {
        $this->beforeForm();

        $model = $this->get($code, ['has_absen']);
        $user = User::where('category', $model->jadwal_category_id)->get();
        $absen = $model->has_absen ?? false;

        return moduleView(modulePathForm(path: self::$is_core), $this->share([
            'model' => $this->get($code),
            'user' => $user,
            'absen' => $absen,
        ]));
    }

    public function postUpdate($code, JadwalRequest $request, UpdateJadwalService $service)
    {
        $data = $service->update($this->model, $request, $code);

        return Response::redirectBack($data);
    }
}
