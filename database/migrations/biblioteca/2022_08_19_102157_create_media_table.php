<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->morphs('model');
            // Using char instead of uuid to avoid breaking change. See https://laravel.com/docs/11.x/upgrade#dedicated-mariadb-driver
            $table->char('uuid', 36)->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            // TODO: check if text instead of json is working with media library ??
            $table->longText('manipulations');
            $table->longText('custom_properties');
            $table->longText('generated_conversions');
            $table->longText('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();

            $table->nullableTimestamps();
        });
    }
}
