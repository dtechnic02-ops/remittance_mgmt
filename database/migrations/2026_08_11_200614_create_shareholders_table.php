<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shareholders', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('mobile', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();

            // Share Information
            $table->unsignedInteger('kitta')->default(0);
            $table->unsignedBigInteger('per_kitta_value')->default(1000);
            $table->unsignedBigInteger('total_investment')->default(0);

            // Shareholder Type / Status
            $table->string('type', 50)->nullable();
            $table->boolean('is_active')->default(true);

            // KYC
            $table->string('photo')->nullable();
            $table->string('citizenship_front')->nullable();
            $table->string('citizenship_back')->nullable();
            $table->string('other_document')->nullable();

            // Additional Information
            $table->text('note')->nullable();

            // Audit
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
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shareholders');
    }
};