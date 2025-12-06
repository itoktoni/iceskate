<?php

namespace App\Charts;

use App\Dao\Models\Core\User;
use App\Dao\Models\Race;
use ArielMejiaDev\LarapexCharts\LarapexChart;

class Dashboard
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build()
    {
        $user = Race::where('race_user_id', 5)->get();

        $data = $asian = [];
        foreach ($user as $item) {
            $asian[] = 47.37;
            $data[] = $item->race_waktu;
            $tanggal[] = $item->race_tanggal;
        }

        // Reverse the data arrays to show smaller values at top
        $reversedData = array_reverse($data);
        $reversedAsian = array_reverse($asian);
        $reversedTanggal = array_reverse($tanggal);

        $chart = $this->chart->lineChart()
            ->setTitle('Performance Bimo')
            ->setSubtitle('Bimo vs Asian Open 2025')
            ->setDataset([
                [
                    'name' => 'Bimo',
                    'data' => $reversedData
                ],
                [
                    'name' => 'Asian Open 2025',
                    'data' => $reversedAsian
                ]
            ])
            ->setXAxis($reversedTanggal)
            ->setGrid()
            ;

         return $chart;
    }
}
