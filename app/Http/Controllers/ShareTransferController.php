<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Services\ShareTransferService;
use App\Services\FinancialDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShareTransferController extends Controller
{
    public function __construct(
        private readonly ShareTransferService $service
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureCanView($request);

        $user = $request->user();

        $query = \App\Models\ShareTransaction::query()
            ->with([
                'shareholder',
                'toShareholder',
                'creator',
            ])
            ->where(
                'transaction_type',
                \App\Models\ShareTransaction::TYPE_TRANSFER
            );

        if ($user->isShareholder()) {
            $shareholder = $user->shareholder()->firstOrFail();

            $query->where(function ($query) use ($shareholder) {
                $query
                    ->where('shareholder_id', $shareholder->id)
                    ->orWhere('to_shareholder_id', $shareholder->id);
            });
        }

        $transactions = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $canCreate = $this->canCreate($request);

        return view(
            'share-transfers.index',
            compact('transactions', 'canCreate')
        );
    }

    public function create(Request $request)
    {
        $this->ensureCanCreate($request);

        $user = $request->user();

        $fromShareholder = null;

        if ($user->isShareholder()) {
            $fromShareholder = $user
                ->shareholder()
                ->where('is_active', true)
                ->firstOrFail();
        }

        $shareholders = Shareholder::query()
            ->where('is_active', true)
            ->when(
                $fromShareholder,
                fn ($query) => $query->whereKeyNot($fromShareholder->id)
            )
            ->orderBy('name')
            ->get();

        return view(
            'share-transfers.create',
            compact(
                'shareholders',
                'fromShareholder'
            )
        );
    }

    public function store(
        Request $request
    ) {
        $this->ensureCanCreate($request);

        $user = $request->user();

        $rules = [
            'to_shareholder_id' => [
                'required',
                'integer',
                'exists:shareholders,id',
            ],

            'date_ad' => [
                'required',
                'date',
            ],

            'date_bs' => [
                'required',
                'string',
                'max:10',
            ],

            'financial_year' => [
                'required',
                'string',
                'max:10',
            ],

            'kitta' => [
                'required',
                'integer',
                'min:1',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:100',
            ],

            'attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],

            'note' => [
                'nullable',
                'string',
            ],
        ];

        if (! $user->isShareholder()) {
            $rules['shareholder_id'] = [
                'required',
                'integer',
                'exists:shareholders,id',
            ];
        }

        $validated = $request->validate($rules);

        $validated = array_replace(
            $validated,
            app(FinancialDateService::class)->fromEnglishDate($validated['date_ad'])
        );

        if ($user->isShareholder()) {
            $fromShareholder = $user
                ->shareholder()
                ->where('is_active', true)
                ->firstOrFail();

            $validated['shareholder_id'] =
                $fromShareholder->id;
        }

        $validated['created_by'] = $user->id;

        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment = $request
                ->file('attachment')
                ->store(
                    'share-transfers/attachments',
                    'local'
                );

            $validated['attachment'] = $attachment;
        }

        try {
            $transaction = $this->service->create(
                $validated
            );
        } catch (\RuntimeException $exception) {
            if ($attachment) {
                Storage::disk('local')
                    ->delete($attachment);
            }

            return back()
                ->withInput()
                ->withErrors([
                    'transaction' =>
                        $exception->getMessage(),
                ]);
        } catch (\Throwable $exception) {
            if ($attachment) {
                Storage::disk('local')
                    ->delete($attachment);
            }

            throw $exception;
        }

        return redirect()
            ->route('share-transfers.index')
            ->with(
                'success',
                'Share transferred successfully.'
            );
    }

    public function convertDate(Request $request)
    {
        $this->ensureCanCreate($request);

        $validated = $request->validate([
            'date_ad' => [
                'required',
                'date',
            ],
        ]);

        $dateBs =
            \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from(
                $validated['date_ad']
            )->toNepaliDate();

        [$year, $month] = array_map(
            'intval',
            explode('-', $dateBs)
        );

        if ($month >= 4) {
            $startYear = $year;
            $endYear = $year + 1;
        } else {
            $startYear = $year - 1;
            $endYear = $year;
        }

        return response()->json([
            'date_bs' => $dateBs,
            'financial_year' =>
                $startYear.'/'.substr(
                    (string) $endYear,
                    -2
                ),
        ]);
    }

    private function ensureCanView(
        Request $request
    ): void {
        $user = $request->user();

        abort_unless($this->canView($request), 403);
    }

    private function ensureCanCreate(
        Request $request
    ): void {
        $user = $request->user();

        abort_unless($this->canCreate($request), 403);
    }

    private function canView(Request $request): bool
    {
        $user = $request->user();

        return (bool) ($user && (
            $user->isAdmin()
            || ($user->isStaff() && $user->hasPermission('share-transfer.view'))
            || $user->isShareholder()
        ));
    }

    private function canCreate(Request $request): bool
    {
        $user = $request->user();

        return (bool) ($user && (
            $user->isAdmin()
            || ($user->isStaff() && $user->hasPermission('share-transfer.create'))
            || ($user->isShareholder() && $user->shareholder()->where('is_active', true)->exists())
        ));
    }
}
