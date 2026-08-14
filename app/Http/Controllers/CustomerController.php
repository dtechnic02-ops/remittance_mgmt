<?php

namespace App\Http\Controllers;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Anuzpandey\LaravelNepaliDate\Exceptions\InvalidDateException;
use App\Models\Customer;
use App\Services\CustomerDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search'));

        $customers = Customer::query()
    ->with('creator')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('account', 'like', "%{$search}%")
                        ->orWhere('branch', 'like', "%{$search}%")
                        ->orWhere('citizenship_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        return view('customers.create', [
            'accountTypes' => Customer::ACCOUNT_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $this->validateCustomer($request);

        $uploadedFiles = [];

        try {
            $customer = DB::transaction(function () use ($request, $validated, &$uploadedFiles) {
            $nextId = (int) Customer::query()
                ->lockForUpdate()
                ->max('id') + 1;

            $validated['customer_code'] = 'CUS-'.str_pad(
                (string) $nextId,
                6,
                '0',
                STR_PAD_LEFT
            );

            if ($request->hasFile('photo')) {
                $validated['photo'] = $request
                    ->file('photo')
                    ->store('customers/photos', 'local');
                $uploadedFiles[] = $validated['photo'];
            }

            if ($request->hasFile('citizenship_front')) {
                $validated['citizenship_front'] = $request
                    ->file('citizenship_front')
                    ->store('customers/citizenship', 'local');
                $uploadedFiles[] = $validated['citizenship_front'];
            }

            if ($request->hasFile('citizenship_back')) {
                $validated['citizenship_back'] = $request
                    ->file('citizenship_back')
                    ->store('customers/citizenship', 'local');
                $uploadedFiles[] = $validated['citizenship_back'];
            }

            $validated['is_active'] = true;
            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            $customer = Customer::create($validated);

            foreach ($request->file('other_documents', []) as $file) {
                $path = $file->store(
                        'customers/other-documents',
                        'local'
                    );
                $uploadedFiles[] = $path;
                $customer->otherDocuments()->create([
                    'file_path' => $path,
                    'uploaded_by' => auth()->id(),
                ]);
            }

            return $customer;
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($uploadedFiles);
            throw $exception;
        }

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $this->ensureAdminOrStaff();

        $customer->load([
            'otherDocuments',
            'creator',
            'updater',
            'deactivator',
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->ensureAdminOrStaff();

        $customer->load('otherDocuments');

        return view('customers.edit', [
            'customer' => $customer,
            'accountTypes' => Customer::ACCOUNT_TYPES,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $this->ensureAdminOrStaff();

        $validated = $this->validateCustomer($request, $customer);

        $uploadedFiles = [];
        $oldFiles = [];

        try {
            DB::transaction(function () use ($request, $customer, $validated, &$uploadedFiles, &$oldFiles) {
            if ($request->hasFile('photo')) {
                $validated['photo'] = $request
                    ->file('photo')
                    ->store('customers/photos', 'local');
                $uploadedFiles[] = $validated['photo'];
                $oldFiles[] = $customer->photo;
            }

            if ($request->hasFile('citizenship_front')) {
                $validated['citizenship_front'] = $request
                    ->file('citizenship_front')
                    ->store('customers/citizenship', 'local');
                $uploadedFiles[] = $validated['citizenship_front'];
                $oldFiles[] = $customer->citizenship_front;
            }

            if ($request->hasFile('citizenship_back')) {
                $validated['citizenship_back'] = $request
                    ->file('citizenship_back')
                    ->store('customers/citizenship', 'local');
                $uploadedFiles[] = $validated['citizenship_back'];
                $oldFiles[] = $customer->citizenship_back;
            }

            $validated['updated_by'] = auth()->id();

            $customer->update($validated);

            foreach ($request->file('other_documents', []) as $file) {
                $path = $file->store(
                        'customers/other-documents',
                        'local'
                    );
                $uploadedFiles[] = $path;
                $customer->otherDocuments()->create([
                    'file_path' => $path,
                    'uploaded_by' => auth()->id(),
                ]);
            }
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($uploadedFiles);
            throw $exception;
        }

        Storage::disk('local')->delete(array_filter($oldFiles));

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $this->ensureAdmin();

        if (! $customer->is_active) {
            return back()->with(
                'error',
                'Customer is already inactive.'
            );
        }

        $customer->update([
            'is_active' => false,
            'deactivated_by' => auth()->id(),
            'deactivated_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deactivated successfully.');
    }

    public function destroyOtherDocument(
        Customer $customer,
        int $document
    ) {
        $this->ensureAdminOrStaff();

        $document = $customer->otherDocuments()
            ->whereKey($document)
            ->firstOrFail();

        $this->deleteFile($document->file_path);

        $document->delete();

        return back()->with(
            'success',
            'Customer document removed successfully.'
        );
    }

    public function convertDate(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
            'english_date' => ['nullable', 'date', 'required_without:nepali_date'],
            'nepali_date' => ['nullable', 'regex:/^\d{4}-\d{2}-\d{2}$/', 'required_without:english_date'],
        ]);

        try {
            if (! empty($validated['english_date'])) {
                if (! $this->isValidEnglishDate($validated['english_date'])) {
                    throw ValidationException::withMessages([
                        'english_date' => 'The English Date is invalid or unsupported.',
                    ]);
                }

                return response()->json([
                    'english_date' => $validated['english_date'],
                    'nepali_date' => LaravelNepaliDate::from($validated['english_date'])
                        ->toNepaliDate('Y-m-d', 'en'),
                ]);
            }

            if (! $this->isValidNepaliDate($validated['nepali_date'])) {
                throw ValidationException::withMessages([
                    'nepali_date' => 'The Nepali Date is invalid or unsupported.',
                ]);
            }

            return response()->json([
                'english_date' => LaravelNepaliDate::from($validated['nepali_date'])
                    ->toEnglishDate('Y-m-d', 'en'),
                'nepali_date' => $validated['nepali_date'],
            ]);
        } catch (InvalidDateException) {
            throw ValidationException::withMessages([
                'date' => 'The supplied English or Nepali date is invalid or unsupported.',
            ]);
        }
    }

    private function validateCustomer(
        Request $request,
        ?Customer $customer = null
    ): array {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'english_date' => [
                'nullable',
                'date',
            ],

            'nepali_date' => [
                'nullable',
                'regex:/^\d{4}-\d{2}-\d{2}$/',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! $this->isValidNepaliDate((string) $value)) {
                        $fail('The Nepali Date is invalid or unsupported.');
                    }
                },
            ],

            'mobile' => [
                'nullable',
                'string',
                'max:30',
            ],

            'phone' => [
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
                'max:255',
            ],

            'account' => [
                'nullable',
                'string',
                'max:100',
            ],

            'branch' => [
                'nullable',
                'string',
                'max:150',
            ],

            'account_type' => [
                'nullable',
                Rule::in(array_keys(Customer::ACCOUNT_TYPES)),
            ],

            'citizenship_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('customers', 'citizenship_number')
                    ->ignore($customer?->id),
            ],

            'note' => [
                'nullable',
                'string',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            'citizenship_front' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            'citizenship_back' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],

            'other_documents' => [
                'nullable',
                'array',
            ],

            'other_documents.*' => [
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ]);

        return $this->synchronizeDates($validated);
    }

    private function synchronizeDates(array $validated): array
    {
        return array_replace($validated, app(CustomerDateService::class)->synchronize(
            $validated['english_date'] ?? null,
            $validated['nepali_date'] ?? null
        ));
    }

    private function isValidEnglishDate(string $date): bool
    {
        try {
            return LaravelNepaliDate::validateEnglish($date);
        } catch (InvalidDateException) {
            return false;
        }
    }

    private function isValidNepaliDate(string $date): bool
    {
        try {
            return LaravelNepaliDate::validateNepali($date);
        } catch (InvalidDateException) {
            return false;
        }
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('local')->delete($path);
        }
    }

    private function ensureAdminOrStaff(): void
    {
        $user = auth()->user();

        abort_unless(
            $user && ($user->isAdmin() || $user->isStaff()),
            403
        );
    }

    private function ensureAdmin(): void
    {
        abort_unless(
            auth()->user()?->isAdmin(),
            403
        );
    }
}
