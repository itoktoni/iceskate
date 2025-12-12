<?php

namespace App\Services;

use App\Dao\Models\Asset;
use App\Dao\Models\Pinjam;
use Plugins\Alert;

class CreatePinjamService
{
    public function save($model, $data)
    {
        $check = false;
        try {
            $check = $model->saveRepository($data->all());
            if (isset($check['status']) && $check['status']) {

                $asset = Asset::find($data->pinjam_asset_id);
                $available = $asset->asset_qty;

                $asset->update([
                    'asset_qty' => $available - $data->pinjam_qty
                ]);

                Alert::create();
            } else {
                $message = env('APP_DEBUG') ? $check['data'] : $check['message'];
                Alert::error($message);
            }
        } catch (\Throwable $th) {
            Alert::error($th->getMessage());

            return $th->getMessage();
        }

        return $check;
    }
}
