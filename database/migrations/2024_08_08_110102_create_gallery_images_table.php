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
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('folder_id'); // Foreign key to folders table
            $table->string('image_path'); // Path to the image
            $table->timestamps(); // created_at and updated_at

            // Define the foreign key constraint
            $table->foreign('folder_id')->references('id')->on('folder')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery_images');
    }
};
