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
        Schema::table('dealers', function (Blueprint $table) {
            $table->dropForeign(['contact_person_id']);

            // Then drop the column itself
            $table->dropColumn('contact_person_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dealers', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_person_id')->nullable();

            // Re-create the foreign key constraint
            $table->foreign('contact_person_id')
                ->references('id')
                ->on('contactPersons')
                ->onDelete('set null');
        });
    }
};
