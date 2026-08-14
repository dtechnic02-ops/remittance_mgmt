<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->date('english_date')->nullable()->after('name');
            $table->string('nepali_date', 10)->nullable()->after('english_date');
            $table->string('account', 100)->nullable()->after('address');
            $table->string('branch', 150)->nullable()->after('account');
            $table->string('account_type', 30)->nullable()->after('branch');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'english_date',
                'nepali_date',
                'account',
                'branch',
                'account_type',
            ]);
        });
    }
};
