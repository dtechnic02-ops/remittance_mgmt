<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            // Source transaction information
            $table->string('transaction_type', 50);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->string('transaction_number', 50);

            // Business dates
            $table->date('date_ad');
            $table->string('date_bs', 10);
            $table->string('financial_year', 10);

            // Money movement
            // increase = money added to this account
            // decrease = money removed from this account
            $table->enum('direction', [
                'increase',
                'decrease',
            ]);

            $table->bigInteger('amount');

            // Balance of this account immediately after this entry
            $table->bigInteger('balance_after');

            // Optional classification for reporting
            $table->string('component', 50)->nullable();
            // examples:
            // principal
            // service_charge
            // commission
            // expense
            // transfer
            // opening
            // capital
            // partner_loan

            $table->text('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Cancellation / reversal audit
            $table->boolean('is_reversal')->default(false);

            $table->unsignedBigInteger('reversal_of_id')->nullable();

            $table->timestamps();

            $table->index([
                'account_id',
                'date_ad',
            ]);

            $table->index([
                'transaction_type',
                'transaction_id',
            ]);

            $table->index('transaction_number');
            $table->index('financial_year');
            $table->index('component');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};