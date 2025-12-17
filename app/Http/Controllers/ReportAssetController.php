<?php

namespace App\Http\Controllers;

use App\Dao\Models\Asset;
use App\Dao\Models\Jarak;
use App\Dao\Models\Race;
use App\Http\Controllers\Core\ReportController;
use Illuminate\Http\Request;
use Plugins\Query;

class ReportAssetController extends ReportController
{
    public $data;

    public function __construct(Race $model)
    {
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

    public function getData()
    {
        $query = Asset::with('has_pinjam', 'has_pinjam.has_user');

        // Join with user data to get payment information
        return $query->get();
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
