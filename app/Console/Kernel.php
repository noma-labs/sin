<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\CreateDatabaseCommand;
use App\Console\Commands\ExifExtractCommand;
use App\Console\Commands\ExifJsonImportCommand;
use App\Console\Commands\SynchPeopleOnPhotosCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

final class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateDatabaseCommand::class,
        ExifExtractCommand::class,
        ExifJsonImportCommand::class,
        SynchPeopleOnPhotosCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void {}

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands(): void
    {
        require base_path('routes/console.php');
    }
}
