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

        $customer = DB::transaction(function () use ($request, $validated) {
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
                    ->store('customers/photos', 'public');
            }

            if ($request->hasFile('citizenship_front')) {
                $validated['citizenship_front'] = $request
                    ->file('citizenship_front')
                    ->store('customers/citizenship', 'public');
            }

            if ($request->hasFile('citizenship_back')) {
                $validated['citizenship_back'] = $request
                    ->file('citizenship_back')
                    ->store('customers/citizenship', 'public');
            }

            $validated['is_active'] = true;
            $validated['created_by'] = auth()->id();
            $validated['updated_by'] = auth()->id();

            $customer = Customer::create($validated);

            foreach ($request->file('other_documents', []) as $file) {
                $customer->otherDocuments()->create([
                    'file_path' => $file->store(
                        'customers/other-documents',
                        'public'
                    ),
                    'uploaded_by' => auth()->id(),
                ]);
            }

            return $customer;
        });

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

        DB::transaction(function () use ($request, $customer, $validated) {
            if ($request->hasFile('photo')) {
                $this->deleteFile($customer->photo);

                $validated['photo'] = $request
                    ->file('photo')
                    ->store('customers/photos', 'public');
            }

            if ($request->hasFile('citizenship_front')) {
                $this->deleteFile($customer->citizenship_front);

                $validated['citizenship_front'] = $request
                    ->file('citizenship_front')
                    ->store('customers/citizenship', 'public');
            }

            if ($request->hasFile('citizenship_back')) {
                $this->deleteFile($customer->citizenship_back);

                $validated['citizenship_back'] = $request
                    ->file('citizenship_back')
                    ->store('customers/citizenship', 'public');
            }

            $validated['updated_by'] = auth()->id();

            $customer->update($validated);

            foreach ($request->file('other_documents', []) as $file) {
                $customer->otherDocuments()->create([
                    'file_path' => $file->store(
                        'customers/other-documents',
                        'public'
                    ),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        });

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
            ],

            'citizenship_front' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
            ],

            'citizenship_back' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
            ],

            'other_documents' => [
                'nullable',
                'array',
            ],

            'other_documents.*' => [
                'image',
                'mimes:jpg,jpeg,png',
            ],
        ]);
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
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