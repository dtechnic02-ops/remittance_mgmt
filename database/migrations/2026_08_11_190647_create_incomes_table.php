<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();

            $table->string('income_number', 50)->unique();

            $table->foreignId('income_category_id')
                ->constrained('income_categories')
                ->restrictOnDelete();

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            $table->date('date_ad');
            $table->string('date_bs', 10);
            $table->string('financial_year', 10);

            $table->unsignedBigInteger('amount');

            $table->string('reference', 100)->nullable();
            $table->text('note')->nullable();
            $table->string('attachment')->nullable();

            $table->string('status', 20)
                ->default('active');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            $table->index('date_ad');
            $table->index('date_bs');
            $table->index('financial_year');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};