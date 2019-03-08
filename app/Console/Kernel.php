<?php

namespace App\Console;

use App\Console\Commands\UpdateParentApiKeys;
use App\Console\Commands\UpdateTravisIpAddresses;
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
        UpdateTravisIpAddresses::class,
        UpdateParentApiKeys::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(UpdateTravisIpAddresses::class)->daily();
    }
}
