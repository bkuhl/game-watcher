<?php

namespace App;

use App\Games\GameUpdater;
use Illuminate\Console\Scheduling\Schedule;

class ConsoleKernel extends \Laravel\Lumen\Console\Kernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        GameUpdater::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(GameUpdater::COMMAND)->hourly();
    }
}
