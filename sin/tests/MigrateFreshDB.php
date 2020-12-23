<?php
namespace Tests;
use Illuminate\Support\Facades\Artisan;

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
        if (!static::$setUpHasRunOnce) {
            Artisan::call('migrate:fresh', ['--database'=> 'db_nomadelfia_test']);
            Artisan::call(
                'db:seed', ['--database'=> 'db_nomadelfia_test']
            );
            
            static::$setUpHasRunOnce = true;
         }
    }
}