<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type')->nullable();
            $table->string('event')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_type')->nullable();
            $table->longText('properties')->nullable();
            $table->char('batch_uuid', 36)->nullable();
            $table->timestamps();

            $table->index(['causer_id', 'causer_type'], 'causer');
            $table->index(['subject_id', 'subject_type'], 'subject');
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id')->index('role_has_permissions_role_id_foreign');

            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create('utenti', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('persona_id')->comment('Connette lutente loggato con la persona nellanagrafe');
            $table->string('username')->nullable()->unique('unq_utenti_username');
            $table->string('email')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onDelete('CASCADE');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->foreign(['role_id'])->references(['id'])->on('roles')->onDelete('CASCADE');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onDelete('CASCADE');
            $table->foreign(['role_id'])->references(['id'])->on('roles')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign('role_has_permissions_permission_id_foreign');
            $table->dropForeign('role_has_permissions_role_id_foreign');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropForeign('model_has_roles_role_id_foreign');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropForeign('model_has_permissions_permission_id_foreign');
        });

        Schema::dropIfExists('utenti');

        Schema::dropIfExists('roles');

        Schema::dropIfExists('role_has_permissions');

        Schema::dropIfExists('permissions');

        Schema::dropIfExists('model_has_roles');

        Schema::dropIfExists('model_has_permissions');

        Schema::dropIfExists('activity_log');
    }
};
