<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersTableAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');  //primary key
            $table->string('name');
            $table->string("username")->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();  // remember_token
            $table->timestamps();     // created_at, updated_at
            $table->softDeletes();    // delete_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
