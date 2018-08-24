<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IsbnFromIntToChar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('libro', function (Blueprint $table) {
            // $table->char('isbn', 13)->change();
            DB::connection('db_biblioteca')->statement('ALTER TABLE libro MODIFY COLUMN isbn CHAR(13)');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('libro', function (Blueprint $table) {
            //
        });
    }
}
