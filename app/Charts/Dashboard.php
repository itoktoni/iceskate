<?php

namespace App\Charts;

use App\Dao\Enums\Core\RoleType;
use App\Dao\Models\Core\User;
use App\Dao\Models\Race;
use Illuminate\Http\Request;

class Dashboard
{
    public function build(Request $request = null)
    {
        // Get user ID from query parameter, default to 5 if not provided
        $user = Race::with('has_user');

        if($request->has('user'))
        {
            $userId = $request->get('user');
            $user = $user->where('race_user_id', $userId);
        }

        $user = $user->get()->mapToGroups(function($item){
            return [$item->race_tanggal => $item];
        });

        $data = $asian = [];
        $tanggal = [];

        foreach ($user as $tgl => $item) {
            $tanggal[] = $tgl;

            foreach ($item as $race) {

                $data[$race->race_user_id][] = $race->race_waktu;
            }

            $asian[] = 47.37;
            $melbourne[] = 45.34;
            $average[] = 44.35;
        }

        $mapping = User::where('role', RoleType::User)->get();
        foreach ($mapping as $value) {

            $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));

            $merge[] = [
                    'label' => $value->name,
                    'data' => isset($data[$value->id]) ? $data[$value->id] : [],
                    'borderColor' => $color,
                    'backgroundColor' => $color,
                    'tension' => 0.1
            ];
        }

        // Return data in Chart.js format
        return [
            'labels' => $tanggal,
            'datasets' => array_merge([
                [
                    'label' => 'Average',
                    'data' => $average,
                    'borderColor' => 'gold',
                    'backgroundColor' => 'gold',
                    'tension' => 0.1
                ],
                [
                    'label' => 'Asian Open 2025',
                    'data' => $asian,
                    'borderColor' => 'green',
                    'backgroundColor' => 'green',
                    'tension' => 0.1
                ],
                [
                    'label' => 'Melbourne Open 2025',
                    'data' => $melbourne,
                    'borderColor' => 'red',
                    'backgroundColor' => 'red',
                    'tension' => 0.1
                ]
            ], $merge)
        ];
    }
}
