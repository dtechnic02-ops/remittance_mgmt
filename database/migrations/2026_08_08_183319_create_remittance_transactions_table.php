<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remittance_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_number', 30)->unique();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();

            $table->foreignId('provider_account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            $table->foreignId('cash_account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            // Business Date
            $table->date('date_ad');
            $table->string('date_bs', 10);
            $table->string('financial_year', 10);

            // NPR only
            $table->bigInteger('principal_amount');
            $table->bigInteger('service_charge')->default(0);

            // Cash received from customer:
            // principal + service charge
            $table->bigInteger('total_cash_received');

            // Optional external/provider reference
            $table->string('provider_reference', 100)->nullable();

            $table->text('note')->nullable();

            // One attachment only: PDF/JPG/JPEG/PNG
            $table->string('attachment')->nullable();

            $table->string('status', 20)->default('active');

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
            $table->index('provider_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remittance_transactions');
    }
};