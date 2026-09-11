<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('share_transactions', function (Blueprint $table) {
            $table->foreignId('settlement_of_id')
                ->nullable()
                ->after('investment_effect')
                ->constrained('share_transactions')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('share_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('settlement_of_id');
        });
    }
};
