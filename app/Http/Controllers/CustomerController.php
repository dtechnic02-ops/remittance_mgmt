<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        return view('customers.create');
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

        return view('customers.edit', compact('customer'));
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

    private function validateCustomer(
        Request $request,
        ?Customer $customer = null
    ): array {
        return $request->validate([
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
