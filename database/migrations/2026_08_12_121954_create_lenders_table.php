<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lenders', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)->unique();

            $table->string('name', 150);

            $table->string('mobile', 30)->nullable();

            $table->string('address', 255)->nullable();

            $table->text('note')->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('name');
            $table->index('mobile');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lenders');
    }
};