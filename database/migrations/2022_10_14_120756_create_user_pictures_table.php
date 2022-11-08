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
        Schema::create('user_pictures', function (Blueprint $table) {
            $table->increments("id")->unique()->index()->comment("AUTO_INCREMENT");
            $table->unsignedInteger("user_id")->index()->comment("Users table id");
            $table->foreign("user_id")->references("id")->on("users");
            $table->string("picture",255);
            $table->string("picture_original",255)->nullable()->comment("Original filename");
            $table->string("picture_thumbnail",255)->nullable()->comment("Resize image");
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
        Schema::dropIfExists('user_pictures');
    }
};
