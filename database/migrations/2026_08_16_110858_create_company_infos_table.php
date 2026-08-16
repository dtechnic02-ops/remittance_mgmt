<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('company_infos', function (Blueprint $table) {
    $table->id();

    $table->string('company_name');

    $table->string('logo')->nullable();
    $table->string('image')->nullable();
    $table->string('thumbnail_image')->nullable();

    $table->text('location')->nullable();

    $table->string('mobile_number')->nullable();
    $table->string('phone_number')->nullable();

    $table->string('email')->nullable();
    $table->string('website')->nullable();

    $table->string('staff_mobile_number')->nullable();
    $table->string('staff_email')->nullable();
    $table->string('staff_photo')->nullable();
    $table->string('staff_title')->nullable();

    $table->longText('description_1')->nullable();
    $table->longText('description_2')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_infos');
    }
};
