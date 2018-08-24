<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PermissionOfPermissionsAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $tableNames = config('permission.table_names');
      Schema::table($tableNames['permissions'], function (Blueprint $table) {
          $table->text("_belong_to_archivio")->nullable()->comment("Contiene il ome dell'archivio a cui il permesso è associato");
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      $tableNames = config('permission.table_names');
      Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropColumn('_belong_to_archivio');
      });
        //
    }
}
