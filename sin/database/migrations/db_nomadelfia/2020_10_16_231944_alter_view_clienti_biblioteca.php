<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterViewClientiBiblioteca extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      //  DB::connection('db_nomadelfia')->S
        DB::statement("CREATE OR REPLACE
                        VIEW v_clienti_biblioteca
                        as 
                        select `db_nomadelfia`.`persone`.`id` AS `id`,`db_nomadelfia`.`persone`.`nominativo` AS `nominativo`,`db_nomadelfia`.`persone`.`data_nascita` AS `data_nascita`,substr(sysdate(),1,4) - substr(`db_nomadelfia`.`persone`.`data_nascita`,1,4) AS `eta` 
                        from `db_nomadelfia`.`persone` 
                        where `db_nomadelfia`.`persone`.`data_nascita` <= sysdate() - interval 70 year_month AND `db_nomadelfia`.`persone`.`stato` = '1'
                        order by `db_nomadelfia`.`persone`.`nominativo` desc");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
