<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opening_balance_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opening_balance_id')
                ->constrained('opening_balances')
                ->restrictOnDelete();
            $table->foreignId('account_id')
                ->constrained('accounts')
                ->restrictOnDelete();
            $table->string('action', 20);
            $table->bigInteger('opening_balance_before');
            $table->bigInteger('opening_balance_after');
            $table->bigInteger('current_balance_before');
            $table->bigInteger('current_balance_after');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opening_balance_adjustments');
    }
};
