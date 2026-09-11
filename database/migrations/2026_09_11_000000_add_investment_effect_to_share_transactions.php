<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('share_transactions', function (Blueprint $table) {
            $table->decimal('investment_effect', 20, 2)
                ->unsigned()
                ->nullable()
                ->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('share_transactions', function (Blueprint $table) {
            $table->dropColumn('investment_effect');
        });
    }
};
