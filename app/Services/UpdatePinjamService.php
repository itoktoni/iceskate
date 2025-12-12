<?php

namespace App\Services;

use App\Dao\Enums\PinjamType;
use App\Dao\Models\Asset;
use Plugins\Alert;

class UpdatePinjamService
{
    public function update($repository, $data, $code)
    {
        $check = $repository->updateRepository($data->all(), $code);
        if ($check['status']) {

            $update = $check['data'];

            if(!empty($data->qty))
            {
                $asset = Asset::find($update->pinjam_asset_id);
                $available = $asset->asset_qty;

                $asset->update([
                    'asset_qty' => $available + $data->qty
                ]);
            }

            if (request()->wantsJson()) {
                return response()->json($check)->getData();
            }
            Alert::update();
        } else {
            Alert::error($check['message']);
        }

        return $check;
    }
}
