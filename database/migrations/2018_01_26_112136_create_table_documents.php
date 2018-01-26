<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('_id')->unique();

            $table->boolean('verified')->default(false);
            $table->unsignedBigInteger('verified_by'); // fk to users

            $table->string('title');
            $table->string('mimetype');
            $table->string('path');
            $table->string('hash');
            $table->string('file_key');

            $table->unsignedBigInteger('created_by'); // fk to users
            $table->unsignedBigInteger('updated_by'); // fk to users
            $table->timestamps(); // New
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
        Schema::dropIfExists('documents');
    }
}
