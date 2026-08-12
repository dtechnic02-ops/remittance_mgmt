<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('remittance_transactions', function (Blueprint $table) {
            $table->string('direction', 20)
                ->default('send')
                ->after('transaction_number');

            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::table('remittance_transactions', function (Blueprint $table) {
            $table->dropIndex(['direction']);
            $table->dropColumn('direction');
        });
    }
};