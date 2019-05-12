<?php

namespace App\Console;

use App\Console\Commands\UserDailyEfficiency;
use App\Console\Commands\UserRisk;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UserDailyEfficiency::class,

        UserRisk::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('UserDailyEfficiency')->dailyAt('00:01');
        $schedule->command('UserRisk')->dailyAt('03:01');

    }
}
