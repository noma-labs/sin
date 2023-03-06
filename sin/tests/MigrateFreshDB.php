<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

trait MigrateFreshDB
{
    /**
     * If true, setup has run at least once.
     * @var boolean
     */
    protected static $setUpHasRunOnce = false;

    /**
     * After the first run of setUp "migrate:fresh --seed"
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        if (App::environment() === 'production') {
            exit();
        }

        if (!static::$setUpHasRunOnce) {
            Artisan::call('make:database');

            Artisan::call('migrate:fresh', ['--database' => 'db_auth', '--path' => "database/migrations/admsys"]);
            Artisan::call('migrate:fresh', ['--database' => 'db_nomadelfia', '--path' => "database/migrations/db_nomadelfia"]);
            Artisan::call('migrate:fresh', ['--database' => 'db_scuola', '--path' => "database/migrations/scuola"]);
            Artisan::call('migrate:fresh', ['--database' => 'db_biblioteca', '--path' => "database/migrations/biblioteca"]);
            Artisan::call('migrate:fresh', ['--database' => 'db_patente', '--path' => "database/migrations/patente"]);
            Artisan::call('migrate:fresh', ['--database' => 'db_officina', '--path' => "database/migrations/officina"]);

//            Artisan::call('migrate');

            Artisan::call('db:seed', ['--class' => 'LocalEnvironmentSeeder']);
            static::$setUpHasRunOnce = true;
        }
    }
}
