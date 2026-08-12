<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShareholderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminOrStaff();

        $search = trim((string) $request->get('search'));

        $shareholders = Shareholder::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'shareholders.index',
            compact('shareholders', 'search')
        );
    }

    public function create()
    {
        $this->ensureAdminOrStaff();

        return view('shareholders.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
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
$validated['kitta'] = 0;
$validated['per_kitta_value'] = 1000;
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
                    ->store('shareholders/photos', 'public');

                $validated['photo'] = $path;
                $uploadedFiles[] = $path;
            }

            if ($request->hasFile('citizenship_front')) {
                $path = $request
                    ->file('citizenship_front')
                    ->store('shareholders/citizenship', 'public');

                $validated['citizenship_front'] = $path;
                $uploadedFiles[] = $path;
            }

            if ($request->hasFile('citizenship_back')) {
                $path = $request
                    ->file('citizenship_back')
                    ->store('shareholders/citizenship', 'public');

                $validated['citizenship_back'] = $path;
                $uploadedFiles[] = $path;
            }

            if ($request->hasFile('other_document')) {
                $path = $request
                    ->file('other_document')
                    ->store('shareholders/documents', 'public');

                $validated['other_document'] = $path;
                $uploadedFiles[] = $path;
            }

            $shareholder = Shareholder::create($validated);
        } catch (\Throwable $exception) {
            foreach ($uploadedFiles as $file) {
                Storage::disk('public')->delete($file);
            }

            throw $exception;
        }

        return redirect()
            ->route('shareholders.show', $shareholder)
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
            compact('shareholder')
        );
    }

    public function edit(Shareholder $shareholder)
    {
        $this->ensureAdminOrStaff();

        return view(
            'shareholders.edit',
            compact('shareholder')
        );
    }

    public function update(
        Request $request,
        Shareholder $shareholder
    ) {
        $this->ensureAdminOrStaff();

        $validated = $request->validate([
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

    

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('photo')) {
            if ($shareholder->photo) {
                Storage::disk('public')
                    ->delete($shareholder->photo);
            }

            $validated['photo'] = $request
                ->file('photo')
                ->store('shareholders/photos', 'public');
        }

        if ($request->hasFile('citizenship_front')) {
            if ($shareholder->citizenship_front) {
                Storage::disk('public')
                    ->delete($shareholder->citizenship_front);
            }

            $validated['citizenship_front'] = $request
                ->file('citizenship_front')
                ->store('shareholders/citizenship', 'public');
        }

        if ($request->hasFile('citizenship_back')) {
            if ($shareholder->citizenship_back) {
                Storage::disk('public')
                    ->delete($shareholder->citizenship_back);
            }

            $validated['citizenship_back'] = $request
                ->file('citizenship_back')
                ->store('shareholders/citizenship', 'public');
        }

        if ($request->hasFile('other_document')) {
            if ($shareholder->other_document) {
                Storage::disk('public')
                    ->delete($shareholder->other_document);
            }

            $validated['other_document'] = $request
                ->file('other_document')
                ->store('shareholders/documents', 'public');
        }

        $shareholder->update($validated);

        return redirect()
            ->route('shareholders.show', $shareholder)
            ->with(
                'success',
                'Shareholder updated successfully.'
            );
    }

    private function ensureAdminOrStaff(): void
    {
        $user = auth()->user();

        abort_unless(
            $user &&
            ($user->isAdmin() || $user->isStaff()),
            403
        );
    }
}