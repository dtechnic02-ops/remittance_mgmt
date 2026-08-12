<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_number', 50)->unique();

            $table->string('transaction_type', 20);
            // buy / withdraw

            $table->foreignId('shareholder_id')
                ->constrained('shareholders')
                ->restrictOnDelete();

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            $table->date('date_ad');
            $table->string('date_bs', 10);
            $table->string('financial_year', 10);

            $table->unsignedInteger('kitta');
            $table->unsignedBigInteger('per_kitta_value');
            $table->unsignedBigInteger('total_amount');

            $table->string('reference', 100)->nullable();

            $table->string('attachment')->nullable();

            $table->text('note')->nullable();

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

            $table->index('transaction_type');
            $table->index('shareholder_id');
            $table->index('account_id');
            $table->index('date_ad');
            $table->index('financial_year');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_transactions');
    }
};