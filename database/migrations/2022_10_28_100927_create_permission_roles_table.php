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
        Schema::create('permission_roles', function (Blueprint $table) {
            $table->increments("id")->unique()->index()->comment("AUTO_INCREMENT");
            $table->unsignedInteger("role_id")->index()->nullable()->comment("Roles table ID");
            $table->foreign("role_id")->references("id")->on("roles")->onDelete('SET NULL');
            $table->unsignedInteger("permission_id")->index()->nullable()->comment("Permissions table ID");
            $table->foreign("permission_id")->references("id")->on("permissions")->onDelete('SET NULL');
            $table->enum("is_permission",['0','1'])->nullable()->comment("0 => 'Inactive', 1 => 'Active'");
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
        Schema::dropIfExists('permission_roles');
    }
};
