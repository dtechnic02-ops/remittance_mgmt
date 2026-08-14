<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerImportController extends Controller
{
    public const HEADERS = [
        'customer_code', 'name', 'english_date', 'nepali_date', 'mobile', 'phone',
        'email', 'address', 'account', 'branch', 'account_type',
        'citizenship_number', 'is_active', 'note',
    ];

    public function create()
    {
        return view('customers.import');
    }

    public function template(): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Customers');
        foreach (self::HEADERS as $index => $header) {
            $sheet->setCellValueExplicit([$index + 1, 1], $header, DataType::TYPE_STRING);
        }
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $instructions = $spreadsheet->createSheet()->setTitle('Instructions');
        $rows = [
            ['Customer Import Instructions'],
            ['Use the Customers sheet. Do not rename, add, remove, or reorder headers.'],
            ['name is required. All other fields are optional. Maximum 500 customers.'],
            ['Dates: YYYY-MM-DD. Supply AD, BS, or both; both must represent the same date.'],
            ['Account types: personal, social_security, allowance, institution, corporate, other.'],
            ['is_active: Yes, No, 1, 0, true, or false. Blank defaults to active.'],
            ['Format phone, account, and citizenship values as Text to preserve leading zeroes.'],
        ];
        $instructions->fromArray($rows);
        $instructions->getColumnDimension('A')->setWidth(110);
        $instructions->getStyle('A1')->getFont()->setBold(true);

        $path = tempnam(sys_get_temp_dir(), 'customer-template-');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return response()->download($path, 'customer-import-template.xlsx')->deleteFileAfterSend(true);
    }

    public function store(Request $request, CustomerDateService $dates)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
        ]);

        try {
            $reader = IOFactory::createReaderForFile($validated['file']->getRealPath());
            abort_unless($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Xlsx, 422);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($validated['file']->getRealPath());
        } catch (\Throwable $exception) {
            if ($exception instanceof ValidationException) {
                throw $exception;
            }
            return back()->withErrors(['file' => 'The uploaded file is not a valid XLSX workbook.']);
        }

        $sheet = $spreadsheet->getSheetByName('Customers') ?? $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestDataColumn();
        $headers = array_map(
            fn ($value) => trim((string) $value),
            $sheet->rangeToArray("A1:{$highestColumn}1", null, true, false)[0] ?? []
        );
        if ($headers !== self::HEADERS) {
            return back()->withErrors(['file' => 'Workbook headers do not exactly match the Customer Excel template.']);
        }

        $rows = [];
        for ($rowNumber = 2; $rowNumber <= $sheet->getHighestDataRow(); $rowNumber++) {
            $values = $sheet->rangeToArray("A{$rowNumber}:N{$rowNumber}", null, true, false)[0];
            if (collect($values)->every(fn ($value) => blank($value))) {
                continue;
            }
            $englishDateCell = $sheet->getCell("C{$rowNumber}");
            if (is_numeric($values[2]) && ExcelDate::isDateTime($englishDateCell)) {
                $values[2] = ExcelDate::excelToDateTimeObject((float) $values[2])->format('Y-m-d');
            }
            $rows[$rowNumber] = array_combine(self::HEADERS, array_map(fn ($v) => is_string($v) ? trim($v) : $v, $values));
        }
        $spreadsheet->disconnectWorksheets();

        if (count($rows) > 500) {
            return back()->withErrors(['file' => 'The workbook exceeds the maximum of 500 customers.']);
        }

        $normalized = [];
        $errors = [];
        $workbookCodes = [];
        foreach ($rows as $rowNumber => $row) {
            $row['account_type'] = $this->normalizeAccountType($row['account_type']);
            $active = $this->normalizeActive($row['is_active']);
            if ($active === null && filled($row['is_active'])) {
                $errors[] = "Row {$rowNumber}: Invalid is_active value.";
            }
            $row['is_active'] = $active ?? true;

            $validator = Validator::make($row, [
                'customer_code' => ['nullable', 'string', 'max:30'],
                'name' => ['required', 'string', 'max:150'],
                'english_date' => ['nullable', 'date_format:Y-m-d'],
                'nepali_date' => ['nullable', 'regex:/^\d{4}-\d{2}-\d{2}$/'],
                'mobile' => ['nullable', 'string', 'max:30'],
                'phone' => ['nullable', 'string', 'max:30'],
                'email' => ['nullable', 'email', 'max:150'],
                'address' => ['nullable', 'string', 'max:255'],
                'account' => ['nullable', 'string', 'max:100'],
                'branch' => ['nullable', 'string', 'max:150'],
                'account_type' => ['nullable', Rule::in(array_keys(Customer::ACCOUNT_TYPES))],
                'citizenship_number' => ['nullable', 'string', 'max:100'],
                'note' => ['nullable', 'string'],
            ]);
            foreach ($validator->errors()->all() as $message) {
                $errors[] = "Row {$rowNumber}: {$message}";
            }
            if ($validator->fails()) {
                continue;
            }

            try {
                $row = array_replace($row, $dates->synchronize($row['english_date'] ?: null, $row['nepali_date'] ?: null));
            } catch (ValidationException $exception) {
                foreach ($exception->errors() as $messages) {
                    foreach ($messages as $message) $errors[] = "Row {$rowNumber}: {$message}";
                }
                continue;
            }

            if (filled($row['customer_code'])) {
                $code = (string) $row['customer_code'];
                if (isset($workbookCodes[$code])) $errors[] = "Row {$rowNumber}: Customer code {$code} is duplicated in the workbook.";
                if (Customer::query()->where('customer_code', $code)->exists()) $errors[] = "Row {$rowNumber}: Customer code {$code} already exists.";
                $workbookCodes[$code] = true;
            }
            $normalized[$rowNumber] = $row;
        }

        if ($errors) {
            throw ValidationException::withMessages(['file' => $errors]);
        }

        DB::transaction(function () use ($normalized, $request) {
            $nextId = (int) Customer::query()->lockForUpdate()->max('id') + 1;
            foreach ($normalized as $row) {
                $row['customer_code'] = filled($row['customer_code'])
                    ? $row['customer_code']
                    : 'CUS-'.str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
                if (Customer::query()->where('customer_code', $row['customer_code'])->exists()) {
                    throw ValidationException::withMessages([
                        'file' => "Customer code {$row['customer_code']} already exists.",
                    ]);
                }
                $row['created_by'] = $request->user()->id;
                $row['updated_by'] = $request->user()->id;
                Customer::create($row);
                $nextId++;
            }
        });

        return redirect()->route('customers.index')->with('success', count($normalized).' customers imported successfully.');
    }

    private function normalizeAccountType(mixed $value): ?string
    {
        if (blank($value)) return null;
        $normalized = strtolower(str_replace([' ', '-'], '_', trim((string) $value)));
        return array_key_exists($normalized, Customer::ACCOUNT_TYPES) ? $normalized : (string) $value;
    }

    private function normalizeActive(mixed $value): ?bool
    {
        if (blank($value)) return null;
        return match (strtolower(trim((string) $value))) {
            'yes', '1', 'true' => true,
            'no', '0', 'false' => false,
            default => null,
        };
    }
}
