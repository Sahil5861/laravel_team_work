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
            // Drop the foreign key constraint first
            $table->dropForeign(['role_id']);

            // Change the column type from bigint to string
            $table->string('role_id')->change();

            // Re-add the foreign key constraint
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
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
            // Drop the foreign key constraint
            $table->dropForeign(['role_id']);

            // Revert the column type back to bigint
            $table->bigInteger('role_id')->change();

            // Re-add the foreign key constraint
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }
};
