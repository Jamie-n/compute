<?php

namespace App\Console;

use App\Console\Commands\ClearTempDirectoryCommand;
use App\Console\Commands\SystemDataRetentionCommand;
use App\Console\Commands\UserAccountDeletionNotifyCommand;
use App\Console\Commands\UserDataRetentionCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(ClearTempDirectoryCommand::class)->hourly();

        $schedule->command(UserDataRetentionCommand::class)->dailyAt('09:00');
        $schedule->command(UserAccountDeletionNotifyCommand::class)->dailyAt('08:00');
        $schedule->command(SystemDataRetentionCommand::class)->dailyAt('07:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
