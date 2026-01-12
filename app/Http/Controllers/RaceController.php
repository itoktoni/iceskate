<?php

namespace App\Http\Controllers;

use App\Dao\Models\Category;
use App\Dao\Models\Core\User;
use App\Dao\Models\Jarak;
use App\Dao\Models\Race;
use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Services\Master\SingleService;
use App\Facades\Model\RaceModel;
use DateTime;
use Hamcrest\Type\IsString;
use Plugins\Alert;
use Plugins\Query;
use Plugins\Response;
use Spatie\SimpleExcel\SimpleExcelReader;

class RaceController extends MasterController
{
    use CreateFunction, UpdateFunction;

    public $insert  = [];

    public function __construct(RaceModel $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
    }

    protected function beforeForm()
    {
        $user = Query::getUser();
        $jadwal = Query::getJadwal();
        $jarak = Jarak::getOptions();

        self::$share = [
            'user' => $user,
            'jadwal' => $jadwal,
            'jarak' => $jarak,
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

    public function getTable()
    {
        $this->beforeForm();

        $data = $this->getData();

        return moduleView(modulePathTable(core: self::$is_core), $this->share([
            'data' => $data,
            'fields' => $this->model::getModel()->getShowField(),
        ]));
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

                $file->storeAs('/public/files/race/', $name);
                $category = [];

                $rows = SimpleExcelReader::create(storage_path('app/public/files/race/' . $name))
                    ->noHeaderRow()
                    ->getRows()
                    ->each(function (array $row) use ($category) {
                        if ($row[0] != "User ID" || $row[0] != 'USER ID') {

                            $nama = $row[0] ?? null;
                            $jarak = $row[1] ?? null;
                            try {
                                $tanggal = is_string($row[2]) ? $row[2] : ($row[2])->format('Y-m-d');
                            } catch (\Throwable $th) {
                                dd($th->getMessage());
                                $tanggal = null;
                            }
                            $waktu = $row[3] ?? null;
                            $keterangan = $row[4] ?? null;

                            $this->insert[] = [
                                'race_user_id' => $nama,
                                'race_jarak_id' => $jarak,
                                'race_tanggal' => $tanggal,
                                'race_waktu' => $waktu,
                                'race_notes' => $keterangan,
                            ];

                        }
                    });

                    // dd($this->insert);

                if(!empty($this->insert))
                {
                    try {

                        Race::insert($this->insert);
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
