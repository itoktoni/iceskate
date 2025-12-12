<?php

namespace App\Services;

use App\Dao\Models\Race;
use Plugins\Alert;

class UpdateJadwalRaceService
{
    public function update($repository, $data, $code)
    {
        $check = $repository->updateRepository($data->all(), $code);

        if ($check['status']) {
            // foreach ($data->code as $key => $value) {
            //     Race::find($value['id'])->update([
            //         'race_waktu' => $value['score'],
            //         'race_jarak' => $data->jarak_id
            //     ]);
            // }

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
