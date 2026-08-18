<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('share_transactions', function (Blueprint $table) {
            $table->foreignId('account_id')
                ->nullable()
                ->change();

            $table->foreignId('to_shareholder_id')
                ->nullable()
                ->after('shareholder_id')
                ->constrained('shareholders')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('share_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('to_shareholder_id');

            $table->foreignId('account_id')
                ->nullable(false)
                ->change();
        });
    }
};