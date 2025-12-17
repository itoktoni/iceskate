<?php

namespace App\Http\Controllers;

use App\Dao\Models\Category;
use App\Dao\Models\Jarak;
use App\Dao\Models\Payment;
use App\Dao\Models\Race;
use App\Http\Controllers\Core\ReportController;
use Illuminate\Http\Request;
use Plugins\Query;

class ReportRaceController extends ReportController
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
        $query = Race::query()
            ->select('*')
            ->leftJoinRelationship('has_jarak')
            ->leftJoinRelationship('has_user')
            ->leftJoinRelationship('has_jadwal');

        if($start = request()->get('start_date'))
        {
            $query = $query->whereDate('race_tanggal','>=', $start);
        }

        if($start = request()->get('end_date'))
        {
            $query = $query->whereDate('race_tanggal', '<=',$start);
        }

        if($category = request()->get('category'))
        {
            $query = $query->where('category', $category);
        }

        if($jarak = request()->get('jarak'))
        {
            $query = $query->where('race_jarak_id', $jarak);
        }


        // Join with user data to get payment information
        return $query->orderBy('race_tanggal', 'ASC')->get();
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
