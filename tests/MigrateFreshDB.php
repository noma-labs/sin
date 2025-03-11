<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;

trait MigrateFreshDB
{
    /**
     * If true, setup has run at least once.
     *
     * @var bool
     */
    protected static $setUpHasRunOnce = false;

    protected function setUp(): void
    {
        parent::setUp();
        if (App::environment() === 'production') {
            exit();
        }

        if (! static::$setUpHasRunOnce) {
            Artisan::call('make:database');

            Artisan::call('migrate:fresh', ['--database' => 'db_auth', '--path' => 'database/migrations/admsys']);
            Artisan::call('migrate:fresh', ['--database' => 'db_nomadelfia', '--path' => 'database/migrations/db_nomadelfia']);
            Artisan::call('migrate:fresh', ['--database' => 'db_scuola', '--path' => 'database/migrations/scuola']);
            Artisan::call('migrate:fresh', ['--database' => 'db_biblioteca', '--path' => 'database/migrations/biblioteca']);
            Artisan::call('migrate:fresh', ['--database' => 'db_patente', '--path' => 'database/migrations/patente']);
            Artisan::call('migrate:fresh', ['--database' => 'db_officina', '--path' => 'database/migrations/officina']);
            Artisan::call('migrate:fresh', ['--database' => 'db_foto', '--path' => 'database/migrations/foto']);
            Artisan::call('migrate:fresh', ['--database' => 'db_rtn', '--path' => 'database/migrations/rtn']);
            Artisan::call('migrate:fresh', ['--database' => 'db_agraria', '--path' => 'database/migrations/agraria']);

            static::$setUpHasRunOnce = true;
        }
    }
}
