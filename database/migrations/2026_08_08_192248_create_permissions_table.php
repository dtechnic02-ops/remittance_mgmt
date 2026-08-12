<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('code', 100)->unique();

            $table->string('group', 100)->nullable();
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('group');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};