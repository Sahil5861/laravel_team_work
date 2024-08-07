<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the foreign key column
            $table->unsignedBigInteger('dealers_id')->nullable()->after('id');

            // Set up the foreign key constraint
            $table->foreign('dealers_id')
                ->references('id')
                ->on('dealers')
                ->onDelete('set null'); // Adjust the behavior as needed
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
            //
        });
    }
};
