<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PermissionsAddColumnsSIUDRA extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      Schema::connection('db_auth')->table('permissions',function (Blueprint $table) {
          $table->dropColumn('_belong_to_archivio');
          $table->dropColumn('name');
          $table->dropColumn('guard_name');
      });

      Schema::connection('db_auth')->table('permissions', function (Blueprint $table) {
          $table->boolean('Select');
          $table->boolean('Insert');
          $table->boolean('Update');
          $table->boolean('Delete');
          $table->boolean('Report');
          $table->boolean('Archivio');
      });
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
