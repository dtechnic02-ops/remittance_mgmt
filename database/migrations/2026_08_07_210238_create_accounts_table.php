<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('code', 50)->unique();

            $table->string('type', 30);
            // cash, bank, remittance, blb, commission, expense, other

            $table->string('account_number', 100)->nullable();
            $table->string('branch_name', 150)->nullable();

            $table->bigInteger('opening_balance')->default(0);
            $table->bigInteger('current_balance')->default(0);

            $table->date('opening_date')->nullable();
            $table->string('opening_date_bs', 20)->nullable();

            $table->boolean('allow_negative')->default(true);
            $table->boolean('is_active')->default(true);

            $table->text('note')->nullable();
            $table->string('attachment')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};