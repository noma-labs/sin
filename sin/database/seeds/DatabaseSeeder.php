<?php

use Illuminate\Database\Seeder;

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
        $this->call(CategoriaTableSeeder::class);
        $this->call(GruppiFamiliariTableSeeder::class);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
