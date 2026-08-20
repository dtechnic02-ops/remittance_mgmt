<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ShareholderController extends Controller
{
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
            ->withExists([
                'shareTransactions as has_share_transactions',
                'receivedShareTransfers as has_received_transfers',
            ])
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
            compact('shareholder')
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
