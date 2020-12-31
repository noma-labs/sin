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
        if (App::environment() === 'production') exit();
        
        if (!static::$setUpHasRunOnce) {
            Artisan::call('migrate:fresh', ['--database'=> 'db_nomadelfia_test', '--path', "database/migrations/db_nomadelfia"]);
            Artisan::call(
                'db:seed', ['--database'=> 'db_nomadelfia_test']
            );
            
            static::$setUpHasRunOnce = true;
         }
    }
}