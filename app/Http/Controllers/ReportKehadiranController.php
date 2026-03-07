<?php

namespace App\Http\Controllers;

use App\Dao\Models\Category;
use App\Dao\Models\Jadwal;
use App\Dao\Models\Jarak;
use App\Dao\Models\Race;
use App\Http\Controllers\Core\ReportController;
use Illuminate\Http\Request;

class ReportKehadiranController extends ReportController
{
    public $data;

    public function __construct(Race $model)
    {
        $this->model = $model::getModel();
    }

    protected function beforeForm()
    {
        $category = Category::getOptions();
        $jarak = Jarak::getOptions();

        self::$share = [
            'category' => $category,
            'jarak' => $jarak,
        ];
    }

    public function getData()
    {
        $query = Jadwal::query()
            ->select('*')
            ->leftJoinRelationship('has_absen');

        if($start = request()->get('start_date'))
        {
            $query = $query->whereDate('jadwal_tanggal','>=', $start);
        }

        if($start = request()->get('end_date'))
        {
            $query = $query->whereDate('jadwal_tanggal', '<=',$start);
        }

        // Join with user data to get payment information
        return $query->orderBy('jadwal_tanggal', 'ASC')->get();
    }

    public function getPrint(Request $request)
    {
        set_time_limit(0);

        $this->data = $this->getData($request);

        return moduleView(modulePathPrint(), $this->share([
            'data' => $this->data,
        ]));
    }
}
