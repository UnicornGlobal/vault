<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id'); // New
            $table->uuid('_id')->unique(); // New
            $table->uuid('api_key')->unique()->nullable(); // New
            $table->string('username')->unique();
            $table->string('password'); // At least they has it :D

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // New
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
