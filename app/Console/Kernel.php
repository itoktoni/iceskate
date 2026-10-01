<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Tagihan bulanan dibuat tiap tanggal 1 jam 00:30
        $schedule->command('generate:payment')->monthlyOn(1, '00:30')->withoutOverlapping();

        // Reminder WA dikirim tiap hari jam 07:00 (termasuk tagihan yang ke-skip)
        $schedule->command('send:payment')->dailyAt('07:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
