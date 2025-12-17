<?php

namespace App\Http\Controllers;

use App\Dao\Enums\Core\RoleType;
use App\Dao\Models\Core\User;
use App\Facades\Model\UserModel;
use App\Http\Controllers\Core\ReportController;
use App\Jobs\JobExportCsvUser;
use Illuminate\Http\Request;

class ReportUserController extends ReportController
{
    public $data;

    public function __construct(UserModel $model)
    {
        $this->model = $model::getModel();
    }

    public function getData()
    {
        $query = User::select('*')
            ->leftJoinRelationship('has_category')
            ->leftJoinRelationship('has_role')
            ->where('role', RoleType::User)
            ->filter();

        if($start = request()->get('start_date'))
        {
            $query = $query->whereDate('created_at','>=', $start);
        }

        if($start = request()->get('end_date'))
        {
            $query = $query->whereDate('created_at', '<=',$start);
        }

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
