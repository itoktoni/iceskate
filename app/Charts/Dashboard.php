<?php

namespace App\Charts;

use App\Dao\Models\Core\User;
use App\Dao\Models\Race;

class Dashboard
{
    public function build()
    {
        $user = Race::where('race_user_id', 5)->get();

        $data = $asian = [];
        $tanggal = [];

        foreach ($user as $item) {
            $asian[] = 47.37;
            $melbourne[] = 45.34;
            $average[] = 44.35;
            $data[] = $item->race_waktu;
            $tanggal[] = $item->race_tanggal;
        }

        // Return data in Chart.js format
        return [
            'labels' => $tanggal,
            'datasets' => [
                [
                    'label' => 'Bimo',
                    'data' => $data,
                    'borderColor' => 'blue',
                    'backgroundColor' => 'blue',
                    'tension' => 0.1
                ],
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
            ]
        ];
    }
}
