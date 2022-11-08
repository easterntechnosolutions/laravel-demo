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
        Schema::create('states', function (Blueprint $table) {
            $table->increments("id")->unique()->index()->comment("AUTO_INCREMENT");
            $table->string("name",255)->nullable();
            $table->unsignedInteger("country_id")->index()->nullable()->comment("Country table ID");
            $table->foreign("country_id")->references("id")->on("countries");
            $table->unsignedInteger('created_by')->nullable()->comment('Users table ID');
            $table->unsignedInteger('updated_by')->nullable()->comment('Users table ID');
            $table->unsignedInteger('deleted_by')->nullable()->comment('Users table ID');
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
        Schema::dropIfExists('states');
    }
};
