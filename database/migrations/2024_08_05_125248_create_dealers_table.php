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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('business_email')->unique();
            $table->string('phone_number');
            $table->unsignedBigInteger('contact_person_id');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->boolean('authenticated')->default(false);
            $table->string('GST_number')->nullable();
            $table->boolean('status')->default(true); // true represents "active", false represents "inactive"

            $table->foreign('contact_person_id')->references('id')->on('ContactPerson')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealers');
    }
};
