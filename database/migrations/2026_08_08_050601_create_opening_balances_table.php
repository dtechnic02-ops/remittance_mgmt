<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_balances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();

            // User selects English Date (AD)
            $table->date('date_ad');

            // Automatically generated Nepali Date (BS)
            $table->string('date_bs', 10);

            // Example: 2083/84
            $table->string('financial_year', 10);

            // NPR only, whole amount
            $table->bigInteger('amount');

            $table->text('note')->nullable();

            // One attachment only:
            // PDF / JPG / JPEG / PNG
            $table->string('attachment')->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // One opening balance per account per financial year
            $table->unique(
                ['account_id', 'financial_year'],
                'opening_balance_account_year_unique'
            );

            $table->index('date_ad');
            $table->index('financial_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_balances');
    }
};