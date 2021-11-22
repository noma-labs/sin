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
    public function setUp()
    {
        parent::setUp();
        if (App::environment() === 'production') {
            exit();
        }
        
        if (!static::$setUpHasRunOnce) {
            Artisan::call('migrate:fresh', ['--database'=> 'db_auth', '--path'=> "database/migrations/admsys"]);
            Artisan::call('migrate:fresh', ['--database'=> 'db_nomadelfia', '--path'=> "database/migrations/db_nomadelfia"]);
            Artisan::call('migrate:fresh', ['--database'=> 'db_scuola', '--path'=> "database/migrations/scuola"]);

            //Artisan::call('db:seed', ['--class'=> 'AuthTablesSeeder']);
            Artisan::call('db:seed', ['--class'=> 'CategoriaTableSeeder']);
            Artisan::call('db:seed', ['--class'=> 'PosizioniTableSeeder']);
            Artisan::call('db:seed', ['--class'=> 'StatiTableSeeder']);
            Artisan::call('db:seed', ['--class'=> 'GruppiFamiliariTableSeeder']);
            Artisan::call('db:seed', ['--class'=> 'AziendeTableSeeder']);
            Artisan::call('db:seed', ['--class'=> 'CaricheTableSeeder']);
            Artisan::call('db:seed', ['--class'=> 'IncarichiTableSeeder']);
            Artisan::call('db:seed', ['--class'=> 'ScuolaTableSeeder']);

        static::$setUpHasRunOnce = true;
        }
    }
}
