<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (App::environment() === 'production') {
            exit();
        }

        Eloquent::unguard();
        // disable check on foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
  
        $this->call(AuthTablesSeeder::class);

        // DB NOMADELIFA
        $this->call(PosizioniTableSeeder::class);
        $this->call(StatiTableSeeder::class);
        $this->call(GruppiFamiliariTableSeeder::class);
        $this->call(IncarichiTableSeeder::class);
        $this->call(VeicoloTableSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
