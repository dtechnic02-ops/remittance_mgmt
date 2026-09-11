<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['code' => 'account.view'],
            [
                'name' => 'Account View',
                'group' => 'Account',
                'description' => 'Allows staff to view account records.',
                'is_active' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('permissions')
            ->where('code', 'account.view')
            ->delete();
    }
};
