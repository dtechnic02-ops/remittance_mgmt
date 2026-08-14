<?php

namespace Tests\Feature;

use App\Http\Controllers\CustomerImportController;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class CustomerImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_routes_enforce_customer_create_permission(): void
    {
        $admin = $this->user('admin');
        $staff = $this->user('staff');
        $allowedStaff = $this->user('staff');
        $shareholder = $this->user('shareholder');
        $this->grant($allowedStaff, 'customer.create');

        $this->actingAs($admin)->get(route('customers.import.create'))->assertOk();
        $this->actingAs($allowedStaff)->get(route('customers.import.create'))->assertOk();
        $this->actingAs($staff)->get(route('customers.import.create'))->assertForbidden();
        $this->actingAs($shareholder)->get(route('customers.import.create'))->assertForbidden();
        auth()->logout();
        $this->get(route('customers.import.create'))->assertRedirect(route('login'));
    }

    public function test_template_download_contains_exact_headers_and_instructions(): void
    {
        $response = $this->actingAs($this->user('admin'))->get(route('customers.import.template'))->assertOk();
        $spreadsheet = IOFactory::load($response->baseResponse->getFile()->getPathname());
        $headers = $spreadsheet->getSheetByName('Customers')->rangeToArray('A1:N1', null, true, false)[0];
        $this->assertSame(CustomerImportController::HEADERS, $headers);
        $this->assertNotNull($spreadsheet->getSheetByName('Instructions'));
    }

    public function test_valid_multi_row_import_is_atomic_and_preserves_text_fields(): void
    {
        $admin = $this->user('admin');
        $file = $this->workbook([
            ['', 'Alpha', '2026-08-14', '', '00980123', '+977-01', 'a@example.com', 'Kathmandu', '000123', 'Main', 'Personal', '00111', 'Yes', 'First'],
            array_fill(0, 14, ''),
            ['CUS-CUSTOM', 'Beta', '', '2083-04-29', '00123', '', '', '', '000999', '', 'Social Security', '00077', 'false', 'Second'],
        ]);

        $this->actingAs($admin)->post(route('customers.import.store'), ['file' => $file])
            ->assertRedirect(route('customers.index'));

        $this->assertDatabaseCount('customers', 2);
        $alpha = Customer::where('name', 'Alpha')->firstOrFail();
        $beta = Customer::where('name', 'Beta')->firstOrFail();
        $this->assertSame('00980123', $alpha->mobile);
        $this->assertSame('000123', $alpha->account);
        $this->assertSame('00111', $alpha->citizenship_number);
        $this->assertSame('personal', $alpha->account_type);
        $this->assertSame('social_security', $beta->account_type);
        $this->assertFalse($beta->is_active);
        $this->assertSame($admin->id, $alpha->created_by);
        $this->assertNotNull($alpha->nepali_date);
        $this->assertNotNull($beta->english_date);
    }

    public function test_invalid_row_rejects_entire_workbook_with_row_numbers(): void
    {
        $file = $this->workbook([
            ['', 'Valid', '', '', '', '', 'valid@example.com', '', '', '', 'corporate', '', '1', ''],
            ['', '', '', '', '', '', 'not-an-email', '', '', '', 'invalid-type', '', 'maybe', ''],
        ]);

        $this->actingAs($this->user('admin'))->post(route('customers.import.store'), ['file' => $file])
            ->assertSessionHasErrors('file');
        $this->assertStringContainsString('Row 3', session('errors')->first('file'));
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_database_and_workbook_duplicate_codes_reject_entire_import(): void
    {
        $admin = $this->user('admin');
        Customer::create(['customer_code' => 'EXISTING', 'name' => 'Existing', 'is_active' => true]);
        $file = $this->workbook([
            ['EXISTING', 'One', '', '', '', '', '', '', '', '', '', '', '', ''],
            ['DUP', 'Two', '', '', '', '', '', '', '', '', '', '', '', ''],
            ['DUP', 'Three', '', '', '', '', '', '', '', '', '', '', '', ''],
        ]);

        $this->actingAs($admin)->post(route('customers.import.store'), ['file' => $file])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('customers', 1);
    }

    public function test_conflicting_dates_and_invalid_active_value_are_rejected(): void
    {
        $file = $this->workbook([
            ['', 'Conflict', '2026-08-14', '2083-01-01', '', '', '', '', '', '', 'other', '', 'sometimes', ''],
        ]);
        $this->actingAs($this->user('admin'))->post(route('customers.import.store'), ['file' => $file])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_wrong_headers_unsupported_type_and_oversized_file_are_rejected(): void
    {
        $admin = $this->user('admin');
        $wrong = $this->workbook([['', 'Name']], ['wrong_header', ...array_slice(CustomerImportController::HEADERS, 1)]);
        $this->actingAs($admin)->post(route('customers.import.store'), ['file' => $wrong])->assertSessionHasErrors('file');

        $this->actingAs($admin)->post(route('customers.import.store'), [
            'file' => UploadedFile::fake()->create('customers.php', 1, 'application/x-php'),
        ])->assertSessionHasErrors('file');
        $this->actingAs($admin)->post(route('customers.import.store'), [
            'file' => UploadedFile::fake()->create('large.xlsx', 5121, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ])->assertSessionHasErrors('file');
    }

    public function test_more_than_500_rows_is_rejected(): void
    {
        $rows = [];
        for ($i = 1; $i <= 501; $i++) $rows[] = ['', "Customer {$i}", '', '', '', '', '', '', '', '', '', '', '', ''];
        $this->actingAs($this->user('admin'))->post(route('customers.import.store'), ['file' => $this->workbook($rows)])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseCount('customers', 0);
    }

    private function workbook(array $rows, ?array $headers = null): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Customers');
        foreach (($headers ?? CustomerImportController::HEADERS) as $column => $value) {
            $sheet->setCellValueExplicit([$column + 1, 1], $value, DataType::TYPE_STRING);
        }
        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $column => $value) {
                $sheet->setCellValueExplicit([$column + 1, $rowIndex + 2], (string) $value, DataType::TYPE_STRING);
            }
        }
        $path = tempnam(sys_get_temp_dir(), 'customers-');
        (new Xlsx($spreadsheet))->save($path);
        return new UploadedFile($path, 'customers.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true, 'email_verified_at' => now()]);
    }

    private function grant(User $user, string $code): void
    {
        $permission = Permission::where('code', $code)->firstOrFail();
        $user->permissions()->attach($permission->id, ['assigned_at' => now()]);
    }
}
