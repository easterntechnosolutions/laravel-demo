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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->comment('AUTO_INCREMENT');
            $table->string('name',255)->nullable();
            $table->string('email',255)->index()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',255)->nullable();
            /*$table->unsignedInteger('role_id',255)->index()->nullable()->comment("Roles table ID");
            $table->foreign('role_id')->references('id')->on('roles');*/
            $table->date("dob")->nullable();
            $table->date("joining_date")->nullable();
            $table->string("joining_time",255)->nullable();
            $table->unsignedInteger("expiry_datetime")->nullable();
            $table->string("profile",255)->nullable();
            $table->string("profile_original",255)->nullable()->comment("Original filename");
            $table->string("profile_thumbnail",255)->nullable()->comment("Resize image");
            $table->enum("gender",['0','1'])->nullable()->comment("0 => 'Female', 1 => 'Male'");
            $table->enum("status",['0','1'])->nullable()->comment("0 => 'Inactive', 1 => 'Active'");
            $table->unsignedInteger("last_login_time")->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->unsignedInteger('created_by')->nullable()->comment('Users table ID');
            $table->unsignedInteger('updated_by')->nullable()->comment('Users table ID');
            $table->unsignedInteger('deleted_by')->nullable()->comment('Users table ID');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
