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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->increments("id")->unique()->index()->comment("AUTO_INCREMENT");
            $table->string("log_name",191)->nullable();
            $table->text("description")->nullable();
            $table->string("ip_address",191)->nullable();
            $table->string("subject_type",191)->nullable();
            $table->unsignedInteger("subject_id")->nullable();
            $table->string("causer_type",191)->nullable();
            $table->unsignedInteger("causer_id")->nullable();
            $table->enum("response_type",['0','1','2','3'])->nullable()->comment("0 => 'Create/Delete response', 1 => 'Update response', 2 => 'Index response', 3 => 'Import/Export response'");
            $table->longText("properties")->nullable();
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
        Schema::dropIfExists('activity_logs');
    }
};
