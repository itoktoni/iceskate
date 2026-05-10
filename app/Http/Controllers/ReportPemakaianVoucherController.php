<?php

namespace App\Http\Controllers;

use App\Dao\Models\History;
use App\Dao\Models\Iuran;
use App\Dao\Models\Payment;
use App\Dao\Models\Voucher;
use App\Http\Controllers\Core\ReportController;
use Illuminate\Http\Request;
use Plugins\Query;

class ReportPemakaianVoucherController extends ReportController
{
    public $data;

    public function __construct(Payment $model)
    {
        $this->model = $model::getModel();
    }

    protected function beforeForm()
    {
        $user = Query::getUser();
        $iuran = Iuran::getOptions();

        self::$share = [
            'user' => $user,
            'iuran' => $iuran,
        ];
    }

    public function getData()
    {
        $query = Voucher::query()
        ->addSelect('*');

        if($user = request()->get('user_id'))
        {
            $query = $query->where('payment_id_user', $user);
        }

        if($iuran = request()->get('iuran_id'))
        {
            $query = $query->where('iuran_id', $iuran);
        }

        if($start = request()->get('start_date'))
        {
            $query = $query->whereDate('payment_tanggal','>=', $start);
        }

        if($start = request()->get('end_date'))
        {
            $query = $query->whereDate('payment_tanggal', '<=',$start);
        }


        // Join with user data to get payment information
        $data =  $query->orderBy('payment_tanggal', 'ASC')->get();

        $map = $data->mapToGroups(function($item){
            return [$item->payment_id => $item];
        }) ?? [];

        return $map;
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
