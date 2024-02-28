<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevertableTable extends Migration
{
    public function up()
    {
        Schema::create('laravel_revertibles', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('group_uuid');

            $table->string('action_class');
            $table->json('parameters', 255);

            $table->string('initial_value')->nullable()->default(null);
            $table->string('result_value')->nullable()->default(null);

            $table->boolean('executed')->default(false);
            $table->boolean('reverted')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('revertible');
    }
}