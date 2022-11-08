<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('import_csv_logs', function (Blueprint $table) {
            $table->increments('id')->index()->comment('AUTO_INCREMENT');
            $table->string('filename',255)->nullable();
            $table->string('file_path',255)->nullable();
            $table->string('model_name',255)->nullable();
            $table->unsignedInteger('user_id')->nullable()->index()->comment('Users table id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('status',['0', '1'])->nullable()->index()->comment('0 - Fail, 1 - Success')->default('0');
            $table->unsignedInteger('no_of_rows')->nullable()->comment('No of csv rows');
            $table->longText('error_log')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_csv_logs');
    }
};
