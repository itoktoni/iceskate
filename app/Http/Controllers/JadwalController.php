<?php

namespace App\Http\Controllers;

use App\Dao\Enums\Core\RoleType;
use App\Dao\Enums\JadwalType;
use App\Dao\Models\Category;
use App\Dao\Models\Core\User;
use App\Dao\Models\Jadwal;
use App\Dao\Models\Jarak;
use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Services\Master\SingleService;
use App\Facades\Model\JadwalModel;
use App\Http\Requests\Core\GeneralRequest;
use App\Http\Requests\Core\JadwalRequest;
use App\Services\UpdateJadwalRaceService;
use App\Services\UpdateJadwalService;
use Plugins\Alert;
use Plugins\Query;
use Spatie\SimpleExcel\SimpleExcelReader;
use Plugins\Response;

class JadwalController extends MasterController
{
    use CreateFunction, UpdateFunction;

    protected function beforeForm()
    {
        $user = Query::getUser();
        $jadwal = JadwalType::getOptions();
        $category = Category::getOptions();
        $jarak = Jarak::getOptions();

        self::$share = [
            'user' => $user,
            'jarak' => $jarak,
            'category' => $category,
            'jadwal' => $jadwal,
            'absen' => collect([]),
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
        $user = User::where('role', RoleType::User)->get();
        $absen = $model->has_absen ?? false;

        return moduleView(modulePathForm(path: self::$is_core), $this->share([
            'model' => $this->get($code),
            'user' => $user,
            'absen' => $absen,
        ]));
    }

    public function postUpdate($code, GeneralRequest $request, UpdateJadwalService $service)
    {
        $data = $service->update($this->model, $request, $code);

        return Response::redirectBack($data);
    }

    public function getRace($code)
    {
        $this->beforeForm();

        $model = $this->get($code, ['has_absen']);
        $user = User::where('role', RoleType::User)->get();

        // $user = Race::addSelect('race.*', 'id', 'name', 'jarak.*')
        //     ->leftJoinRelationship('has_user')
        //     ->leftJoinRelationship('has_jarak')
        //     ->where('race_jadwal_id', $model->field_primary)
        //     ->get();
        $absen = $model->has_absen ?? [];

        return moduleView(modulePathForm('race'), $this->share([
            'model' => $this->get($code),
            'user' => $user,
            'absen' => $absen,
        ]));
    }

    public function postRace($code, JadwalRequest $request, UpdateJadwalRaceService $service)
    {
        $data = $service->update($this->model, $request, $code);

        return Response::redirectBack($data);
    }

    public function postTable()
    {
        if (request()->exists('delete')) {
            if (empty(request()->get('code'))) {
                Alert::error('Pilih data yang akan di hapus');
                return redirect()->back();
            }

            $code = array_unique(request()->get('code'));
            $data = self::$service->delete($this->model, $code);
        }

        if (request()->has('file')) {
            $file = request()->file('file');

            if (!empty($file)) {

                set_time_limit(0);
                ini_set('max_execution_time', 0);

                $extension = $file->extension();
                $name = time() . '.' . $extension;

                $file->storeAs('/public/files/jadwal/', $name);
                $category = [];

                $rows = SimpleExcelReader::create(storage_path('app/public/files/jadwal/' . $name))
                    ->noHeaderRow()
                    ->getRows()
                    ->each(function (array $row) use ($category) {
                        if ($row[0] != "Nama Jadwal") {

                            $nama = $row[0] ?? null;
                            $tanggal = ($row[1])->format('Y-m-d') ?? null;
                            $keterangan = $row[2] ?? null;

                            $this->insert[] = [
                                'jadwal_nama' => $nama,
                                'jadwal_tanggal' => $tanggal,
                                'jadwal_keterangan' => $keterangan,
                            ];

                        }
                    });

                    // dd($this->insert);

                if(!empty($this->insert))
                {
                    try {

                        Jadwal::insert($this->insert);
                        Alert::create('Data berhasil di upload');

                    } catch (\Throwable $th) {
                        Alert::error($th->getMessage());
                    }
                }
            }

        }

        return Response::redirectBack();
    }
}
