<?php

namespace Tests\Feature;

use App\Http\Controllers\ShareholderController;
use App\Models\Permission;
use App\Models\Shareholder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ShareholderImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_shareholder_starts_at_zero_and_edit_cannot_change_share_balances(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->post(route('shareholders.store'), [
            'code' => 'SH-ZERO', 'name' => 'Zero Owner', 'is_active' => 1,
            'kitta' => 50, 'per_kitta_value' => 9000, 'total_investment' => 450000,
        ])->assertRedirect();

        $shareholder = Shareholder::where('code', 'SH-ZERO')->firstOrFail();
        $this->assertSame(0, $shareholder->kitta);
        $this->assertSame(0, $shareholder->total_investment);

        $shareholder->update(['kitta' => 3, 'per_kitta_value' => 700, 'total_investment' => 2100]);
        $this->actingAs($admin)->put(route('shareholders.update', $shareholder), [
            'code' => 'SH-ZERO', 'name' => 'Updated Owner', 'is_active' => 1,
            'kitta' => 99, 'per_kitta_value' => 9999, 'total_investment' => 999999,
        ])->assertRedirect();

        $shareholder->refresh();
        $this->assertSame('Updated Owner', $shareholder->name);
        $this->assertSame(3, $shareholder->kitta);
        $this->assertSame(700, $shareholder->per_kitta_value);
        $this->assertSame(2100, $shareholder->total_investment);
    }

    public function test_valid_import_creates_zero_balance_shareholders_and_normalizes_active(): void
    {
        $admin = $this->user('admin');
        $file = $this->workbook([
            ['SH-001', 'Alpha', '00980123', 'alpha@example.test', 'Kathmandu', 'Yes', 'First'],
            ['SH-002', 'Beta', '', '', '', 'false', 'Second'],
        ]);

        $this->actingAs($admin)->post(route('shareholders.import.store'), ['file' => $file])
            ->assertRedirect(route('shareholders.index'));

        $this->assertDatabaseCount('shareholders', 2);
        $alpha = Shareholder::where('code', 'SH-001')->firstOrFail();
        $beta = Shareholder::where('code', 'SH-002')->firstOrFail();
        $this->assertSame('00980123', $alpha->mobile);
        $this->assertSame(0, $alpha->kitta);
        $this->assertSame(0, $alpha->total_investment);
        $this->assertTrue($alpha->is_active);
        $this->assertFalse($beta->is_active);
    }

    public function test_duplicate_code_rejects_entire_import(): void
    {
        Shareholder::create(['code' => 'EXISTING', 'name' => 'Existing']);
        $file = $this->workbook([
            ['NEW', 'New', '', '', '', '', ''],
            ['EXISTING', 'Existing Again', '', '', '', '', ''],
            ['NEW', 'Duplicate New', '', '', '', '', ''],
        ]);

        $this->actingAs($this->user('admin'))
            ->post(route('shareholders.import.store'), ['file' => $file])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseCount('shareholders', 1);
    }

    public function test_wrong_headers_and_invalid_active_reject_import(): void
    {
        $admin = $this->user('admin');
        $wrong = $this->workbook(
            [['SH-1', 'Wrong', '', '', '', '', '']],
            ['wrong', ...array_slice(ShareholderController::IMPORT_HEADERS, 1)]
        );
        $this->actingAs($admin)->post(route('shareholders.import.store'), ['file' => $wrong])
            ->assertSessionHasErrors('file');

        $invalid = $this->workbook([['SH-2', 'Invalid', '', '', '', 'sometimes', '']]);
        $this->actingAs($admin)->post(route('shareholders.import.store'), ['file' => $invalid])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseCount('shareholders', 0);
    }

    public function test_more_than_500_rows_is_rejected(): void
    {
        $rows = [];
        for ($index = 1; $index <= 501; $index++) {
            $rows[] = ["SH-{$index}", "Shareholder {$index}", '', '', '', '', ''];
        }

        $this->actingAs($this->user('admin'))
            ->post(route('shareholders.import.store'), ['file' => $this->workbook($rows)])
            ->assertSessionHasErrors('file');
        $this->assertDatabaseCount('shareholders', 0);
    }

    public function test_import_routes_require_shareholder_create_permission(): void
    {
        $admin = $this->user('admin');
        $staff = $this->user('staff');
        $allowedStaff = $this->user('staff');
        $shareholder = $this->user('shareholder');
        $permission = Permission::where('code', 'shareholder.create')->firstOrFail();
        $allowedStaff->permissions()->attach($permission->id, ['assigned_at' => now()]);

        $this->actingAs($admin)->get(route('shareholders.import.create'))->assertOk();
        $this->actingAs($allowedStaff)->get(route('shareholders.import.create'))->assertOk();
        $this->actingAs($staff)->get(route('shareholders.import.create'))->assertForbidden();
        $this->actingAs($shareholder)->get(route('shareholders.import.create'))->assertForbidden();
        auth()->logout();
        $this->get(route('shareholders.import.create'))->assertRedirect(route('login'));
    }

    private function workbook(array $rows, ?array $headers = null): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Shareholders');
        foreach (($headers ?? ShareholderController::IMPORT_HEADERS) as $column => $value) {
            $sheet->setCellValueExplicit([$column + 1, 1], $value, DataType::TYPE_STRING);
        }
        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $column => $value) {
                $sheet->setCellValueExplicit(
                    [$column + 1, $rowIndex + 2],
                    (string) $value,
                    DataType::TYPE_STRING
                );
            }
        }
        $path = tempnam(sys_get_temp_dir(), 'shareholders-');
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile(
            $path,
            'shareholders.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
