<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $modules = [
            'customer' => ['view', 'create', 'update', 'delete'],
            'remittance' => ['view', 'create', 'cancel'],
            'account-transfer' => ['view', 'create', 'cancel'],
            'ledger' => ['view'],
            'expense-category' => ['view', 'manage'],
            'expense' => ['view', 'create', 'cancel'],
            'income-category' => ['view', 'manage'],
            'income' => ['view', 'create', 'cancel'],
            'shareholder' => ['view', 'create', 'update'],
            'share-transaction' => ['view', 'create', 'cancel'],
            'lender' => ['view', 'create', 'update'],
            'borrowing' => ['view', 'create', 'cancel'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                DB::table('permissions')->updateOrInsert(
                    ['code' => $module.'.'.$action],
                    [
                        'name' => ucwords(str_replace('-', ' ', $module)).' '.ucfirst($action),
                        'group' => ucwords(str_replace('-', ' ', $module)),
                        'description' => 'Allows staff to '.$action.' '.str_replace('-', ' ', $module).' records.',
                        'is_active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('code', [
            'customer.view', 'customer.create', 'customer.update', 'customer.delete',
            'remittance.view', 'remittance.create',
            'account-transfer.view', 'account-transfer.create', 'account-transfer.cancel',
            'ledger.view', 'expense-category.view', 'expense-category.manage',
            'expense.view', 'expense.create', 'expense.cancel',
            'income-category.view', 'income-category.manage',
            'income.view', 'income.create', 'income.cancel',
            'shareholder.view', 'shareholder.create', 'shareholder.update',
            'share-transaction.view', 'share-transaction.create', 'share-transaction.cancel',
            'lender.view', 'lender.create', 'lender.update',
            'borrowing.view', 'borrowing.create', 'borrowing.cancel',
        ])->delete();
    }
};
