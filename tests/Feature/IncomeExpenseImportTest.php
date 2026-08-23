<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Permission;
use App\Models\User;
use App\Services\IncomeExpenseImportService;
use App\Services\FinancialDateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class IncomeExpenseImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_income_import_posts_balances_ledgers_and_authoritative_dates(): void
    {
        $admin = $this->user('admin');
        $this->incomeCategory('INC');
        $account = $this->account('CASH', 100);

        $this->actingAs($admin)->post(route('incomes.import.preview'), ['file' => $this->workbook('Income', [
            ['2026-08-12', 'INC', 'CASH', '50', 'A', 'First'],
            ['2026-08-13', 'INC', 'CASH', '70', 'B', 'Second'],
        ])])->assertOk()->assertSee('Valid');
        $this->post(route('incomes.import.confirm'))->assertRedirect(route('incomes.index'));

        $this->assertDatabaseCount('incomes', 2);
        $this->assertDatabaseCount('ledger_entries', 2);
        $this->assertSame(220, $account->fresh()->current_balance);
        $income = Income::where('reference', 'A')->firstOrFail();
        $expectedDate = app(FinancialDateService::class)->fromEnglishDate('2026-08-12');
        $this->assertSame($expectedDate['date_bs'], $income->date_bs);
        $this->assertSame($expectedDate['financial_year'], $income->financial_year);
        $this->assertDatabaseHas('ledger_entries', [
            'transaction_type' => 'income', 'transaction_id' => $income->id,
            'direction' => 'increase', 'amount' => 50,
        ]);
    }

    public function test_valid_expense_import_posts_sequential_balance_and_ledgers(): void
    {
        $admin = $this->user('admin');
        $this->expenseCategory('EXP');
        $account = $this->account('BANK', 300, Account::TYPE_BANK);

        $this->actingAs($admin)->post(route('expenses.import.preview'), ['file' => $this->workbook('Expense', [
            ['2026-08-12', 'EXP', 'BANK', '100', 'A', 'First'],
            ['2026-08-13', 'EXP', 'BANK', '50', 'B', 'Second'],
        ])])->assertOk();
        $this->post(route('expenses.import.confirm'))->assertRedirect(route('expenses.index'));

        $this->assertDatabaseCount('expenses', 2);
        $this->assertDatabaseCount('ledger_entries', 2);
        $this->assertSame(150, $account->fresh()->current_balance);
        $expense = Expense::where('reference', 'A')->firstOrFail();
        $expectedDate = app(FinancialDateService::class)->fromEnglishDate('2026-08-12');
        $this->assertSame($expectedDate['date_bs'], $expense->date_bs);
        $this->assertSame($expectedDate['financial_year'], $expense->financial_year);
        $this->assertDatabaseHas('ledger_entries', [
            'transaction_type' => 'expense', 'transaction_id' => $expense->id,
            'direction' => 'decrease', 'amount' => 100,
        ]);
    }

    public function test_invalid_category_account_and_fixed_deposit_are_rejected(): void
    {
        $admin = $this->user('admin');
        $this->incomeCategory('INC');
        $this->account('CASH', 100);
        $this->account('FD', 1000, Account::TYPE_FIXED_DEPOSIT);

        foreach ([
            ['2026-08-12', 'MISSING', 'CASH', '1', '', ''],
            ['2026-08-12', 'INC', 'MISSING', '1', '', ''],
            ['2026-08-12', 'INC', 'FD', '1', '', ''],
        ] as $row) {
            $this->actingAs($admin)->post(route('incomes.import.preview'), [
                'file' => $this->workbook('Income', [$row]),
            ])->assertSessionHasErrors('file');
        }
        $this->assertDatabaseCount('incomes', 0);
        $this->assertDatabaseCount('ledger_entries', 0);
    }

    public function test_expense_invalid_mapping_and_fixed_deposit_are_rejected(): void
    {
        $admin = $this->user('admin');
        $this->expenseCategory('EXP');
        $this->account('CASH', 100);
        $this->account('FD', 1000, Account::TYPE_FIXED_DEPOSIT);

        foreach ([
            ['2026-08-12', 'MISSING', 'CASH', '1', 'A', ''],
            ['2026-08-12', 'EXP', 'MISSING', '1', 'B', ''],
            ['2026-08-12', 'EXP', 'FD', '1', 'C', ''],
        ] as $row) {
            $this->actingAs($admin)->post(route('expenses.import.preview'), [
                'file' => $this->workbook('Expense', [$row]),
            ])->assertSessionHasErrors('file');
        }

        $this->assertDatabaseCount('expenses', 0);
        $this->assertDatabaseCount('ledger_entries', 0);
    }

    public function test_expense_preview_rejects_sequential_negative_balance(): void
    {
        $admin = $this->user('admin');
        $this->expenseCategory('EXP');
        $this->account('CASH', 100);

        $this->actingAs($admin)->post(route('expenses.import.preview'), ['file' => $this->workbook('Expense', [
            ['2026-08-12', 'EXP', 'CASH', '60', 'FIRST', ''],
            ['2026-08-12', 'EXP', 'CASH', '60', 'SECOND', ''],
        ])])->assertSessionHasErrors('file');
        $this->assertStringContainsString('negative', session('errors')->first('file'));
        $this->assertDatabaseCount('expenses', 0);
    }

    public function test_confirm_failure_rolls_back_every_expense_effect(): void
    {
        $admin = $this->user('admin');
        $this->expenseCategory('EXP');
        $account = $this->account('CASH', 200);
        $this->actingAs($admin)->post(route('expenses.import.preview'), ['file' => $this->workbook('Expense', [
            ['2026-08-12', 'EXP', 'CASH', '60', 'FIRST', ''],
            ['2026-08-12', 'EXP', 'CASH', '60', 'SECOND', ''],
        ])])->assertOk();

        $account->update(['current_balance' => 100]);
        $this->post(route('expenses.import.confirm'))->assertSessionHasErrors('file');
        $this->assertSame(100, $account->fresh()->current_balance);
        $this->assertDatabaseCount('expenses', 0);
        $this->assertDatabaseCount('ledger_entries', 0);
    }

    public function test_wrong_headers_and_more_than_500_rows_are_rejected(): void
    {
        $admin = $this->user('admin');
        $wrong = ['wrong', ...array_slice(IncomeExpenseImportService::HEADERS, 1)];
        $this->actingAs($admin)->post(route('incomes.import.preview'), [
            'file' => $this->workbook('Income', [['2026-08-12', 'X', 'Y', '1', '', '']], $wrong),
        ])->assertSessionHasErrors('file');

        $rows = [];
        for ($index = 1; $index <= 501; $index++) {
            $rows[] = ['2026-08-12', 'EXP', 'CASH', '1', '', ''];
        }
        $this->actingAs($admin)->post(route('expenses.import.preview'), [
            'file' => $this->workbook('Expense', $rows),
        ])->assertSessionHasErrors('file');
    }

    public function test_import_routes_enforce_existing_create_permissions(): void
    {
        $admin = $this->user('admin');
        $staff = $this->user('staff');
        $allowed = $this->user('staff');
        $incomePermission = Permission::where('code', 'income.create')->firstOrFail();
        $expensePermission = Permission::where('code', 'expense.create')->firstOrFail();
        $allowed->permissions()->attach($incomePermission->id, ['assigned_at' => now()]);
        $allowed->permissions()->attach($expensePermission->id, ['assigned_at' => now()]);

        $this->actingAs($admin)->get(route('incomes.import.create'))->assertOk();
        $this->actingAs($admin)->get(route('expenses.import.create'))->assertOk();
        $this->actingAs($allowed)->get(route('incomes.import.create'))->assertOk();
        $this->actingAs($allowed)->get(route('expenses.import.create'))->assertOk();
        $this->actingAs($staff)->get(route('incomes.import.create'))->assertForbidden();
        $this->actingAs($staff)->get(route('expenses.import.create'))->assertForbidden();
        $this->actingAs($staff)->get(route('incomes.import.template'))->assertForbidden();
        $this->actingAs($staff)->get(route('expenses.import.template'))->assertForbidden();
        auth()->logout();
        $this->get(route('incomes.import.create'))->assertRedirect(route('login'));
    }

    private function workbook(string $sheetName, array $rows, ?array $headers = null): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle($sheetName);
        foreach (($headers ?? IncomeExpenseImportService::HEADERS) as $column => $value) {
            $sheet->setCellValueExplicit([$column + 1, 1], $value, DataType::TYPE_STRING);
        }
        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $column => $value) {
                $sheet->setCellValueExplicit([$column + 1, $rowIndex + 2], (string) $value, DataType::TYPE_STRING);
            }
        }
        $path = tempnam(sys_get_temp_dir(), 'financial-import-');
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'import.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true, 'email_verified_at' => now()]);
    }

    private function incomeCategory(string $code): IncomeCategory
    {
        return IncomeCategory::create(['name' => 'Income '.$code, 'code' => $code, 'is_active' => true]);
    }

    private function expenseCategory(string $code): ExpenseCategory
    {
        return ExpenseCategory::create(['name' => 'Expense '.$code, 'code' => $code, 'is_active' => true]);
    }

    private function account(string $code, int $balance, string $type = Account::TYPE_CASH): Account
    {
        return Account::create([
            'name' => $code, 'code' => $code, 'type' => $type,
            'opening_balance' => $balance, 'current_balance' => $balance, 'is_active' => true,
        ]);
    }
}
