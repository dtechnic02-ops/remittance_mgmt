<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('customer_code', 30)->unique();

            $table->string('name', 150);
            $table->string('mobile', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();

            $table->string('address', 255)->nullable();

            $table->string('citizenship_number', 100)->nullable();

            $table->string('photo')->nullable();
            $table->string('citizenship_front')->nullable();
            $table->string('citizenship_back')->nullable();

            $table->boolean('is_active')->default(true);

            $table->text('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('deactivated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('deactivated_at')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('mobile');
            $table->index('citizenship_number');
            $table->index('is_active');
        });

        Schema::create('customer_other_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->string('file_path');

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_other_documents');
        Schema::dropIfExists('customers');
    }
};