<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageFieldsToAdditionalImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('additional_images', function (Blueprint $table) {
            $table->string('image_medium')->nullable();
            $table->string('image_small')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('additional_images', function (Blueprint $table) {
            $table->dropColumn('image_medium');
            $table->dropColumn('image_small');
        });
    }
}
