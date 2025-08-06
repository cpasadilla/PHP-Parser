<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */    protected $commands = [
        \App\Console\Commands\ListSoaNumbers::class,
        \App\Console\Commands\GenerateSoaNumber::class,
        \App\Console\Commands\CheckNewYear::class,
        \App\Console\Commands\ResetSoaNumbers::class,
        \App\Console\Commands\ResetInterestActivation::class,
        \App\Console\Commands\UpdateLocationsCase::class,
    ];/**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run at midnight on January 1st
        $schedule->command('soa:check-new-year')
                ->yearly()
                ->at('00:01')
                ->description('Reset SOA numbering sequence for the new year');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
