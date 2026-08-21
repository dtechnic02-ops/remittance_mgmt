<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ShareholderController extends Controller
{
    public const IMPORT_HEADERS = [
        'code',
        'name',
        'mobile',
        'email',
        'address',
        'active',
        'note',
    ];

    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search', ''));

        $status = strtolower(
            trim((string) $request->get('status', 'active'))
        );

        if (! in_array($status, [
            'active',
            'cancelled',
            'all',
        ], true)) {
            $status = 'active';
        }

        /*
        |--------------------------------------------------------------------------
        | Active Shareholder Summary
        |--------------------------------------------------------------------------
        */

        $activeSummary = Shareholder::query()
            ->where('is_active', true)
            ->selectRaw(
                'COUNT(*) as shareholder_count,
                 COALESCE(SUM(kitta), 0) as total_kitta,
                 COALESCE(SUM(total_investment), 0) as total_share_capital'
            )
            ->first();

        $summary = [
            'shareholder_count' =>
                (int) $activeSummary->shareholder_count,

            'total_kitta' =>
                (int) $activeSummary->total_kitta,

            'total_share_capital' =>
                (int) $activeSummary->total_share_capital,
        ];

        /*
        |--------------------------------------------------------------------------
        | Shareholder List
        |--------------------------------------------------------------------------
        */

        $shareholders = Shareholder::query()
            ->when(
                $status === 'active',
                fn ($query) =>
                    $query->where('is_active', true)
            )
            ->when(
                $status === 'cancelled',
                fn ($query) =>
                    $query->where('is_active', false)
            )
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where(
                            'code',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'mobile',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'address',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'shareholders.index',
            compact(
                'shareholders',
                'search',
                'status',
                'summary'
            )
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        return view('shareholders.create', [
            'shareholderUsers' =>
                $this->availableShareholderUsers(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'user_id' => $this->userLinkRules(),

            'code' => [
                'required',
                'string',
                'max:50',
                'unique:shareholders,code',
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'mobile' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            'citizenship_front' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'citizenship_back' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'other_document' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ]);

        if (! auth()->user()->isAdmin()) {
            unset($validated['user_id']);
        }

        /*
        |--------------------------------------------------------------------------
        | Share quantity is financial data.
        | New Shareholder always starts from zero.
        |--------------------------------------------------------------------------
        */

        $validated['kitta'] = 0;
        $validated['total_investment'] = 0;

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $uploadedFiles = [];

        try {
            if ($request->hasFile('photo')) {
                $path = $request
                    ->file('photo')
                    ->store(
                        'shareholders/photos',
                        'local'
                    );

                $validated['photo'] = $path;
                $uploadedFiles[] = $path;
            }

            if ($request->hasFile('citizenship_front')) {
                $path = $request
                    ->file('citizenship_front')
                    ->store(
                        'shareholders/citizenship',
                        'local'
                    );

                $validated['citizenship_front'] = $path;
                $uploadedFiles[] = $path;
            }

            if ($request->hasFile('citizenship_back')) {
                $path = $request
                    ->file('citizenship_back')
                    ->store(
                        'shareholders/citizenship',
                        'local'
                    );

                $validated['citizenship_back'] = $path;
                $uploadedFiles[] = $path;
            }

            if ($request->hasFile('other_document')) {
                $path = $request
                    ->file('other_document')
                    ->store(
                        'shareholders/documents',
                        'local'
                    );

                $validated['other_document'] = $path;
                $uploadedFiles[] = $path;
            }

            $shareholder =
                Shareholder::create($validated);
        } catch (\Throwable $exception) {
            Storage::disk('local')
                ->delete($uploadedFiles);

            throw $exception;
        }

        return redirect()
            ->route(
                'shareholders.show',
                $shareholder
            )
            ->with(
                'success',
                'Shareholder created successfully.'
            );
    }

    public function show(Shareholder $shareholder)
    {
        $this->ensureAdminOrStaff();

        $shareholder->load([
            'creator',
            'updater',
        ]);

        return view(
            'shareholders.show',
            [
                'shareholder' => $shareholder,
                'canHardDelete' => $shareholder->canHardDelete(),
            ]
        );
    }

    public function edit(Shareholder $shareholder)
    {
        $this->ensureAdminOrStaff();

        return view(
            'shareholders.edit',
            [
                'shareholder' => $shareholder,

                'shareholderUsers' =>
                    $this->availableShareholderUsers(
                        $shareholder
                    ),
            ]
        );
    }

    public function update(
        Request $request,
        Shareholder $shareholder
    ) {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'user_id' =>
                $this->userLinkRules($shareholder),

            'code' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'shareholders',
                    'code'
                )->ignore($shareholder->id),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'mobile' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            'citizenship_front' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'citizenship_back' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'other_document' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ]);

        if (! auth()->user()->isAdmin()) {
            unset($validated['user_id']);
        }

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['updated_by'] =
            auth()->id();

        $newFiles = [];
        $oldFiles = [];

        if ($request->hasFile('photo')) {
            $validated['photo'] =
                $request
                    ->file('photo')
                    ->store(
                        'shareholders/photos',
                        'local'
                    );

            $newFiles[] =
                $validated['photo'];

            $oldFiles[] =
                $shareholder->photo;
        }

        if ($request->hasFile('citizenship_front')) {
            $validated['citizenship_front'] =
                $request
                    ->file('citizenship_front')
                    ->store(
                        'shareholders/citizenship',
                        'local'
                    );

            $newFiles[] =
                $validated['citizenship_front'];

            $oldFiles[] =
                $shareholder->citizenship_front;
        }

        if ($request->hasFile('citizenship_back')) {
            $validated['citizenship_back'] =
                $request
                    ->file('citizenship_back')
                    ->store(
                        'shareholders/citizenship',
                        'local'
                    );

            $newFiles[] =
                $validated['citizenship_back'];

            $oldFiles[] =
                $shareholder->citizenship_back;
        }

        if ($request->hasFile('other_document')) {
            $validated['other_document'] =
                $request
                    ->file('other_document')
                    ->store(
                        'shareholders/documents',
                        'local'
                    );

            $newFiles[] =
                $validated['other_document'];

            $oldFiles[] =
                $shareholder->other_document;
        }

        try {
            $shareholder->update($validated);
        } catch (\Throwable $exception) {
            Storage::disk('local')
                ->delete($newFiles);

            throw $exception;
        }

        Storage::disk('local')
            ->delete(
                array_filter($oldFiles)
            );

        return redirect()
            ->route(
                'shareholders.show',
                $shareholder
            )
            ->with(
                'success',
                'Shareholder updated successfully.'
            );
    }

    public function importCreate()
    {
        return view('shareholders.import');
    }

    public function importTemplate(): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Shareholders');

        foreach (self::IMPORT_HEADERS as $index => $header) {
            $sheet->setCellValueExplicit(
                [$index + 1, 1],
                $header,
                DataType::TYPE_STRING
            );
        }

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        $instructions = $spreadsheet->createSheet()->setTitle('Instructions');
        $instructions->fromArray([
            ['Shareholder Import Instructions'],
            ['Use the Shareholders sheet without renaming, adding, removing, or reordering headers.'],
            ['code and name are required. Maximum 500 shareholders.'],
            ['active accepts Yes, No, 1, 0, true, or false. Blank defaults to active.'],
            ['Imported shareholders start with zero Kitta and zero investment.'],
        ]);
        $instructions->getColumnDimension('A')->setWidth(110);
        $instructions->getStyle('A1')->getFont()->setBold(true);

        $path = tempnam(sys_get_temp_dir(), 'shareholder-template-');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return response()
            ->download($path, 'shareholder-import-template.xlsx')
            ->deleteFileAfterSend(true);
    }

    public function importStore(Request $request)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
        ]);

        try {
            $reader = IOFactory::createReaderForFile(
                $validated['file']->getRealPath()
            );

            if (! $reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Xlsx) {
                throw new \RuntimeException('Unsupported workbook type.');
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load(
                $validated['file']->getRealPath()
            );
        } catch (\Throwable $exception) {
            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            return back()->withErrors([
                'file' => 'The uploaded file is not a valid XLSX workbook.',
            ]);
        }

        $sheet = $spreadsheet->getSheetByName('Shareholders')
            ?? $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestDataColumn();
        $headers = array_map(
            fn ($value) => trim((string) $value),
            $sheet->rangeToArray(
                "A1:{$highestColumn}1",
                null,
                true,
                false
            )[0] ?? []
        );

        if ($headers !== self::IMPORT_HEADERS) {
            $spreadsheet->disconnectWorksheets();

            return back()->withErrors([
                'file' => 'Workbook headers do not exactly match the Shareholder Excel template.',
            ]);
        }

        $rows = [];
        for ($rowNumber = 2; $rowNumber <= $sheet->getHighestDataRow(); $rowNumber++) {
            $values = $sheet->rangeToArray(
                "A{$rowNumber}:G{$rowNumber}",
                null,
                true,
                false
            )[0];

            if (collect($values)->every(fn ($value) => blank($value))) {
                continue;
            }

            $rows[$rowNumber] = array_combine(
                self::IMPORT_HEADERS,
                array_map(
                    fn ($value) => is_string($value) ? trim($value) : $value,
                    $values
                )
            );
        }
        $spreadsheet->disconnectWorksheets();

        if (count($rows) > 500) {
            return back()->withErrors([
                'file' => 'The workbook exceeds the maximum of 500 shareholders.',
            ]);
        }

        $normalized = [];
        $errors = [];
        $workbookCodes = [];

        foreach ($rows as $rowNumber => $row) {
            $active = $this->normalizeImportActive($row['active']);

            if ($active === null && filled($row['active'])) {
                $errors[] = "Row {$rowNumber}: Invalid active value.";
            }

            $row['is_active'] = $active ?? true;
            unset($row['active']);

            $validator = Validator::make($row, [
                'code' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:150'],
                'mobile' => ['nullable', 'string', 'max:30'],
                'email' => ['nullable', 'email', 'max:150'],
                'address' => ['nullable', 'string'],
                'note' => ['nullable', 'string'],
                'is_active' => ['required', 'boolean'],
            ]);

            foreach ($validator->errors()->all() as $message) {
                $errors[] = "Row {$rowNumber}: {$message}";
            }

            if ($validator->fails()) {
                continue;
            }

            $code = (string) $row['code'];
            if (isset($workbookCodes[$code])) {
                $errors[] = "Row {$rowNumber}: Shareholder code {$code} is duplicated in the workbook.";
            }
            if (Shareholder::query()->where('code', $code)->exists()) {
                $errors[] = "Row {$rowNumber}: Shareholder code {$code} already exists.";
            }
            $workbookCodes[$code] = true;
            $normalized[$rowNumber] = $row;
        }

        if ($errors) {
            throw ValidationException::withMessages(['file' => $errors]);
        }

        DB::transaction(function () use ($normalized, $request) {
            foreach ($normalized as $row) {
                if (Shareholder::query()->lockForUpdate()->where('code', $row['code'])->exists()) {
                    throw ValidationException::withMessages([
                        'file' => "Shareholder code {$row['code']} already exists.",
                    ]);
                }

                Shareholder::create($row + [
                    'kitta' => 0,
                    'total_investment' => 0,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);
            }
        });

        return redirect()
            ->route('shareholders.index')
            ->with('success', count($normalized).' shareholders imported successfully.');
    }

    private function normalizeImportActive(mixed $value): ?bool
    {
        if (blank($value)) {
            return null;
        }

        return match (strtolower(trim((string) $value))) {
            'yes', '1', 'true' => true,
            'no', '0', 'false' => false,
            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel Shareholder
    |--------------------------------------------------------------------------
    |
    | Historical shareholder is never deleted.
    | is_active = false.
    | Linked shareholder login is also blocked.
    |
    */

    public function cancel(
        Shareholder $shareholder
    ) {
        $this->ensureAdminOrStaff();

        DB::transaction(function () use (
            $shareholder
        ) {
            $locked = Shareholder::query()
                ->lockForUpdate()
                ->findOrFail($shareholder->id);

            if (! $locked->is_active) {
                return;
            }

            $locked->update([
                'is_active' => false,
                'updated_by' => auth()->id(),
            ]);

            if ($locked->user_id) {
                User::query()
                    ->whereKey($locked->user_id)
                    ->update([
                        'is_active' => false,
                    ]);
            }
        });

        return redirect()
            ->route(
                'shareholders.index',
                ['status' => 'active']
            )
            ->with(
                'success',
                'Shareholder cancelled successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Hard Delete
    |--------------------------------------------------------------------------
    |
    | Allowed only when:
    | - No share transaction history
    | - Kitta = 0
    | - Total Investment = 0
    |
    */

    public function destroy(
        Shareholder $shareholder
    ) {
        $this->ensureAdminOrStaff();

        $files = [];

        DB::transaction(function () use (
            $shareholder,
            &$files
        ) {
            $locked = Shareholder::query()
                ->lockForUpdate()
                ->findOrFail($shareholder->id);

            if (! $locked->canHardDelete()) {
                abort(
                    422,
                    'This shareholder has share history or financial balance and cannot be deleted. Cancel the shareholder instead.'
                );
            }

            $files = array_filter([
                $locked->photo,
                $locked->citizenship_front,
                $locked->citizenship_back,
                $locked->other_document,
            ]);

            if ($locked->user_id) {
                User::query()
                    ->whereKey($locked->user_id)
                    ->update([
                        'is_active' => false,
                    ]);
            }

            $locked->delete();
        });

        if ($files) {
            Storage::disk('local')
                ->delete($files);
        }

        return redirect()
            ->route('shareholders.index')
            ->with(
                'success',
                'Unused shareholder deleted permanently.'
            );
    }

    private function ensureAdminOrStaff(): void
    {
        $user = auth()->user();

        abort_unless(
            $user &&
            (
                $user->canAccessBusinessData()
            ),
            403
        );
    }

    private function availableShareholderUsers(
        ?Shareholder $shareholder = null
    ) {
        if (! auth()->user()->isAdmin()) {
            return collect();
        }

        return User::query()
            ->where(
                'role',
                'shareholder'
            )
            ->where(function ($query) use (
                $shareholder
            ) {
                $query
                    ->whereDoesntHave(
                        'shareholder'
                    );

                if ($shareholder?->user_id) {
                    $query->orWhereKey(
                        $shareholder->user_id
                    );
                }
            })
            ->orderBy('name')
            ->get();
    }

    private function userLinkRules(
        ?Shareholder $shareholder = null
    ): array {
        if (! auth()->user()->isAdmin()) {
            return ['prohibited'];
        }

        return [
            'nullable',
            'integer',

            Rule::exists(
                'users',
                'id'
            )->where(
                fn ($query) =>
                    $query->where(
                        'role',
                        'shareholder'
                    )
            ),

            Rule::unique(
                'shareholders',
                'user_id'
            )->ignore(
                $shareholder?->id
            ),
        ];
    }
}
