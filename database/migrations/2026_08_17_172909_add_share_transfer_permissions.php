<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            [
                'code' => 'share-transfer.view',
                'name' => 'Share Transfer View',
                'group' => 'Share Transfer',
                'description' => 'Allows viewing share transfer records.',
            ],
            [
                'code' => 'share-transfer.create',
                'name' => 'Share Transfer Create',
                'group' => 'Share Transfer',
                'description' => 'Allows creating shareholder to shareholder share transfers.',
            ],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['code' => $permission['code']],
                [
                    'name' => $permission['name'],
                    'group' => $permission['group'],
                    'description' => $permission['description'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('code', [
                'share-transfer.view',
                'share-transfer.create',
            ])
            ->delete();
    }
};