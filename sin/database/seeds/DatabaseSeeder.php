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
      if (App::environment() === 'production') exit();

        Eloquent::unguard();
        // disable check on foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate all tables of the auth database, except migrations
        $tables = DB::connection('db_auth')->select('SHOW TABLES');
        foreach ($tables as $table) {
            if ($table->Tables_in_archivio_auth !== 'migrations')
                DB::connection('db_auth')->table($table->Tables_in_archivio_auth)->truncate();
        }

         $this->call(AuthTablesSeeder::class);

         DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
