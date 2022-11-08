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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger("country_id")->index()->nullable()->comment("Country table ID");
            $table->foreign("country_id")->references("id")->on("countries")->onDelete('SET NULL');
            $table->unsignedInteger("state_id")->index()->nullable()->comment("State table ID");
            $table->foreign("state_id")->references("id")->on("states")->onDelete('SET NULL');
            $table->unsignedInteger("city_id")->index()->nullable()->comment("City table ID");
            $table->foreign("city_id")->references("id")->on("cities")->onDelete('SET NULL');
            $table->unsignedInteger("hobby_id")->index()->nullable()->comment("Hobby table ID");
            $table->foreign("hobby_id")->references("id")->on("hobbies")->onDelete('SET NULL');
            $table->string("phone_no",255)->nullable();
            $table->string("address",255)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Drop foreign key constraints
            $table->dropForeign(['country_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['hobby_id']);

            // 2. Drop the column
            $table->dropColumn('country_id');
            $table->dropColumn('state_id');
            $table->dropColumn('city_id');
            $table->dropColumn('hobby_id');
            $table->dropColumn('phone_no');
            $table->dropColumn('address');
        });
    }
};
