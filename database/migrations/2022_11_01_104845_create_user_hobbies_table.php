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
        Schema::create('user_hobbies', function (Blueprint $table) {
            $table->unsignedInteger("user_id")->index()->nullable()->comment("Users table ID");
            $table->foreign("user_id")->references("id")->on("users")->onDelete('SET NULL');
            $table->unsignedInteger("hobby_id")->index()->nullable()->comment("Hobbies table ID");
            $table->foreign("hobby_id")->references("id")->on("hobbies")->onDelete('SET NULL');
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
        Schema::dropIfExists('user_hobbies');
    }
};
