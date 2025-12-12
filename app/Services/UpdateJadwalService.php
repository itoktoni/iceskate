<?php

namespace App\Services;

use App\Dao\Models\Race;
use Plugins\Alert;

class UpdateJadwalService
{
    public function update($repository, $data, $code)
    {
        $check = $repository->updateRepository($data->all(), $code);
        if ($check['status']) {

            $check['data']->has_absen()->sync($data->code);

            Race::where('race_jadwal_id', $code)->delete();
            Race::insert($data->race);

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
