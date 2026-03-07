<?php

namespace App\Http\Controllers;

use App\Dao\Enums\Core\BooleanType;
use App\Dao\Enums\IuranType;
use App\Dao\Models\Iuran;
use App\Facades\Model\IuranModel;
use App\Http\Controllers\Core\MasterController;
use App\Http\Function\CreateFunction;
use App\Http\Function\UpdateFunction;
use App\Services\Master\SingleService;
use Plugins\Alert;
use Plugins\Response;
use Spatie\SimpleExcel\SimpleExcelReader;

class IuranController extends MasterController
{
    use CreateFunction, UpdateFunction;

 protected function beforeForm()
    {
        $iuran = IuranType::getOptions();
        $boolean = BooleanType::getOptions();

        self::$share = [
            'iuran' => $iuran,
            'boolean' => $boolean,
        ];
    }

    public function __construct(IuranModel $model, SingleService $service)
    {
        self::$service = self::$service ?? $service;
        $this->model = $model::getModel();
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

                $file->storeAs('/public/files/iuran/', $name);
                $category = [];

                $rows = SimpleExcelReader::create(storage_path('app/public/files/iuran/' . $name))
                    ->noHeaderRow()
                    ->getRows()
                    ->each(function (array $row) use ($category) {
                        if ($row[0] != "Nama") {

                            $nama = $row[0] ?? null;
                            $tanggal = ($row[1])->format('Y-m-d') ?? null;
                            $harga = $row[2] ?? null;
                            $type = $row[3] ?? null;

                            $this->insert[] = [
                                'iuran_nama' => $nama,
                                'iuran_tanggal' => $tanggal,
                                'iuran_harga' => $harga,
                                'iuran_type' => $type,
                            ];

                        }
                    });

                    // dd($this->insert);

                if(!empty($this->insert))
                {
                    try {

                        Iuran::insert($this->insert);
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
