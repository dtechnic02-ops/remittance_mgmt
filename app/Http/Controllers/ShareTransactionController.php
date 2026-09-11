<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Services\FinancialDateService;
use App\Services\ShareTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ShareTransactionService;
use App\Services\ShareTransactionExportService;


class ShareTransactionController extends Controller
{
public function __construct(
    private readonly ShareTransactionService $shareTransactionService,
    private readonly ShareTransferService $shareTransferService,
    private readonly ShareTransactionExportService $exportService
) {
}
    public function index(Request $request)
{
    $user = $request->user();

    abort_unless($user, 403);

    $canViewShareTransaction =
        $user->isAdmin()
        || $user->isHelpDesk()
        || (
            $user->isStaff()
            && $user->hasPermission('share-transaction.view')
        );

    $canViewTransfer =
        $user->isAdmin()
        || $user->isHelpDesk()
        || (
            $user->isStaff()
            && $user->hasPermission('share-transfer.view')
        )
        || $user->isShareholder();

    abort_unless(
        $canViewShareTransaction || $canViewTransfer,
        403
    );

    $search = trim(
        (string) $request->get('search', '')
    );

    $type = strtolower(
        trim(
            (string) $request->get('type', 'all')
        )
    );

    if (! in_array(
        $type,
        [
            'all',
            ShareTransaction::TYPE_BUY,
            ShareTransaction::TYPE_WITHDRAW,
            ShareTransaction::TYPE_TRANSFER,
        ],
        true
    )) {
        $type = 'all';
    }

    /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    |
    | Default = Active
    | All = no status condition
    |
    */

    $status = strtolower(
        trim(
            (string) $request->get(
                'status',
                'active'
            )
        )
    );

    if (! in_array(
        $status,
        [
            'all',
            'active',
            'cancelled',
        ],
        true
    )) {
        $status = 'active';
    }

    /*
    |--------------------------------------------------------------------------
    | Financial Year
    |--------------------------------------------------------------------------
    */

    $currentFinancialYear =
        app(FinancialDateService::class)
            ->fromEnglishDate(
                now()->toDateString()
            )['financial_year'];

    $financialYear = trim(
        (string) $request->get(
            'financial_year',
            $currentFinancialYear
        )
    );

    if ($financialYear === '') {
        $financialYear =
            $currentFinancialYear;
    }

    $fromDate = trim(
        (string) $request->get(
            'from_date',
            ''
        )
    );

    $toDate = trim(
        (string) $request->get(
            'to_date',
            ''
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Allowed Transaction Types
    |--------------------------------------------------------------------------
    */

    $allowedTypes = [];

    if ($canViewShareTransaction) {
        $allowedTypes[] =
            ShareTransaction::TYPE_BUY;

        $allowedTypes[] =
            ShareTransaction::TYPE_WITHDRAW;
    }

    if ($canViewTransfer) {
        $allowedTypes[] =
            ShareTransaction::TYPE_TRANSFER;
    }

    /*
    |--------------------------------------------------------------------------
    | Financial Year Options
    |--------------------------------------------------------------------------
    */

    $financialYears =
        ShareTransaction::query()
            ->whereIn(
                'transaction_type',
                $allowedTypes
            )
            ->whereNotNull(
                'financial_year'
            )
            ->distinct()
            ->orderByDesc(
                'financial_year'
            )
            ->pluck(
                'financial_year'
            );

    if (
        ! $financialYears->contains(
            $currentFinancialYear
        )
    ) {
        $financialYears->prepend(
            $currentFinancialYear
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    $query =
        ShareTransaction::query()
            ->with([
                'shareholder',
                'toShareholder',
                'account',
                'creator',
                'canceller',
            ])
            ->whereIn(
                'transaction_type',
                $allowedTypes
            );

    /*
    |--------------------------------------------------------------------------
    | Shareholder Login
    |--------------------------------------------------------------------------
    */

    if ($user->isShareholder()) {
        $shareholder = $user
            ->shareholder()
            ->firstOrFail();

        $query
            ->where(
                'transaction_type',
                ShareTransaction::TYPE_TRANSFER
            )
            ->where(
                function ($query) use (
                    $shareholder
                ) {
                    $query
                        ->where(
                            'shareholder_id',
                            $shareholder->id
                        )
                        ->orWhere(
                            'to_shareholder_id',
                            $shareholder->id
                        );
                }
            );

        $type =
            ShareTransaction::TYPE_TRANSFER;
    }

    /*
    |--------------------------------------------------------------------------
    | Type Filter
    |--------------------------------------------------------------------------
    */

    if (
        $type !== 'all'
        && in_array(
            $type,
            $allowedTypes,
            true
        )
    ) {
        $query->where(
            'transaction_type',
            $type
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

    if ($status !== 'all') {
        $query->where(
            'status',
            $status
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FY Filter
    |--------------------------------------------------------------------------
    */

    if (
        strtolower($financialYear)
        !== 'all'
    ) {
        $query->where(
            'financial_year',
            $financialYear
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Date Filter
    |--------------------------------------------------------------------------
    */

    if ($fromDate !== '') {
        $query->whereDate(
            'date_ad',
            '>=',
            $fromDate
        );
    }

    if ($toDate !== '') {
        $query->whereDate(
            'date_ad',
            '<=',
            $toDate
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($search !== '') {
        $query->where(
            function ($query) use (
                $search
            ) {
                $query
                    ->where(
                        'transaction_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'shareholder',
                        function ($query) use (
                            $search
                        ) {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'mobile',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'toShareholder',
                        function ($query) use (
                            $search
                        ) {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'mobile',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    )
                    ->orWhereHas(
                        'account',
                        function ($query) use (
                            $search
                        ) {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'code',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            }
        );
    }

    $filteredTransactions = null;

    if (in_array($request->get('output'), ['print', 'excel'], true)) {
        $filteredTransactions = (clone $query)
            ->orderByDesc('date_ad')
            ->orderByDesc('id')
            ->get();

        if ($request->get('output') === 'excel') {
            return $this->exportService->excel($filteredTransactions);
        }

    }

    $summaryShareholderId = null;

    if ($user->isShareholder()) {
        $summaryShareholderId = (int) $shareholder->id;
    } elseif ($search !== '') {
        $matchingShareholders = Shareholder::query()
            ->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            })
            ->limit(2)
            ->pluck('id');

        if ($matchingShareholders->count() === 1) {
            $summaryShareholderId = (int) $matchingShareholders->first();
        }
    }

    if ($summaryShareholderId !== null) {
        $filteredSummary = (clone $query)
            ->selectRaw(
                'COUNT(*) as total_records,
                 COALESCE(SUM(CASE
                    WHEN transaction_type = ? AND shareholder_id = ? THEN kitta
                    WHEN transaction_type = ? AND shareholder_id = ? THEN -kitta
                    WHEN transaction_type = ? AND to_shareholder_id = ? THEN kitta
                    WHEN transaction_type = ? AND shareholder_id = ? THEN -kitta
                    ELSE 0
                 END), 0) as total_kitta,
                 COALESCE(SUM(CASE
                    WHEN transaction_type = ? AND shareholder_id = ? THEN total_amount
                    WHEN transaction_type = ? AND shareholder_id = ? THEN -total_amount
                    WHEN transaction_type = ? AND to_shareholder_id = ? THEN total_amount
                    WHEN transaction_type = ? AND shareholder_id = ? THEN -total_amount
                    ELSE 0
                 END), 0) as total_amount',
                [
                    ShareTransaction::TYPE_BUY,
                    $summaryShareholderId,
                    ShareTransaction::TYPE_WITHDRAW,
                    $summaryShareholderId,
                    ShareTransaction::TYPE_TRANSFER,
                    $summaryShareholderId,
                    ShareTransaction::TYPE_TRANSFER,
                    $summaryShareholderId,
                    ShareTransaction::TYPE_BUY,
                    $summaryShareholderId,
                    ShareTransaction::TYPE_WITHDRAW,
                    $summaryShareholderId,
                    ShareTransaction::TYPE_TRANSFER,
                    $summaryShareholderId,
                    ShareTransaction::TYPE_TRANSFER,
                    $summaryShareholderId,
                ]
            )
            ->first();
    } else {
        $filteredSummary = (clone $query)
            ->selectRaw(
                'COUNT(*) as total_records,
                 COALESCE(SUM(CASE
                    WHEN transaction_type = ? THEN kitta
                    WHEN transaction_type = ? THEN -kitta
                    ELSE 0
                 END), 0) as total_kitta,
                 COALESCE(SUM(CASE
                    WHEN transaction_type = ? THEN total_amount
                    WHEN transaction_type = ? THEN -total_amount
                    ELSE 0
                 END), 0) as total_amount',
                [
                    ShareTransaction::TYPE_BUY,
                    ShareTransaction::TYPE_WITHDRAW,
                    ShareTransaction::TYPE_BUY,
                    ShareTransaction::TYPE_WITHDRAW,
                ]
            )
            ->first();
    }
    $totalRecords = (int) $filteredSummary->total_records;
    $totalAmount = (int) $filteredSummary->total_amount;

    if ($request->get('output') === 'print') {
        return view('share-transactions.print', [
            'transactions' => $filteredTransactions,
            'totalKitta' => (int) $filteredSummary->total_kitta,
            'totalAmount' => (float) $filteredSummary->total_amount,
        ]);
    }

    $transactions =
        $query
            ->orderByDesc('date_ad')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

    $canCreateShareTransaction =
        $this->canCreateShareTransaction(
            $request
        );

    $canCreateTransfer =
        $this->canCreateTransfer(
            $request
        );

    $canEditShareTransaction =
        $canCreateShareTransaction;

    $canEditTransfer =
        $canCreateTransfer;

    return view(
        'share-transactions.index',
        compact(
            'transactions',
            'search',
            'type',
            'status',
            'financialYear',
            'financialYears',
            'currentFinancialYear',
            'fromDate',
            'toDate',
            'canViewShareTransaction',
            'canViewTransfer',
            'canCreateShareTransaction',
            'canCreateTransfer',
            'canEditShareTransaction',
            'canEditTransfer',
            'totalRecords',
            'totalAmount'
        )
    );
}

    public function create(Request $request)
    {
        $canCreateShareTransaction =
            $this->canCreateShareTransaction($request);

        $canCreateTransfer =
            $this->canCreateTransfer($request);

        abort_unless(
            $canCreateShareTransaction
            || $canCreateTransfer,
            403
        );

        $user = $request->user();

        $fromShareholder = null;

        if ($user->isShareholder()) {
            $fromShareholder =
                $user
                    ->shareholder()
                    ->where('is_active', true)
                    ->firstOrFail();
        }

        $shareholders =
            Shareholder::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

        $accounts =
            Account::query()
                ->where('is_active', true)
                ->where(
                    'type',
                    '!=',
                    Account::TYPE_FIXED_DEPOSIT
                )
                ->orderBy('name')
                ->get();

        $requestedType = strtolower(
            (string) $request->get(
                'type',
                ''
            )
        );

        if ($user->isShareholder()) {
            $defaultType =
                ShareTransaction::TYPE_TRANSFER;
        } elseif (
            $requestedType
            === ShareTransaction::TYPE_TRANSFER
            && $canCreateTransfer
        ) {
            $defaultType =
                ShareTransaction::TYPE_TRANSFER;
        } else {
            $defaultType =
                ShareTransaction::TYPE_BUY;
        }

        return view(
            'share-transactions.create',
            compact(
                'shareholders',
                'accounts',
                'fromShareholder',
                'canCreateShareTransaction',
                'canCreateTransfer',
                'defaultType'
            )
        );
    }

    public function store(Request $request)
    {
        $transactionType = strtolower(
            trim(
                (string) $request->input(
                    'transaction_type'
                )
            )
        );

        if (
            $transactionType
            === ShareTransaction::TYPE_TRANSFER
        ) {
            return $this->storeTransferInternal(
                $request
            );
        }

        return $this->storeBuyWithdrawInternal(
            $request
        );
    }

    private function storeBuyWithdrawInternal(
        Request $request
    ) {
        $this->ensureCanCreateShareTransaction(
            $request
        );

        $validated =
            $request->validate([
                'transaction_type' => [
                    'required',
                    'in:buy,withdraw',
                ],

                'shareholder_id' => [
                    'required',
                    'exists:shareholders,id',
                ],

                'account_id' => [
                    'required',
                    'exists:accounts,id',
                ],

                'date_ad' => [
                    'required',
                    'date',
                ],

                'date_bs' => [
                    'nullable',
                    'string',
                    'max:10',
                ],

                'financial_year' => [
                    'nullable',
                    'string',
                    'max:10',
                ],

                'kitta' => [
                    'required',
                    'integer',
                    'min:1',
                ],

                'per_kitta_value' => [
                    'required',
                    'numeric',
                    'decimal:0,2',
                    'min:0.01',
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
                    'max:2000',
                ],
            ]);

        $validated = array_replace(
            $validated,
            app(FinancialDateService::class)
                ->fromEnglishDate(
                    $validated['date_ad']
                )
        );

        $shareholder =
            Shareholder::query()
                ->whereKey(
                    $validated['shareholder_id']
                )
                ->where('is_active', true)
                ->firstOrFail();

        $account =
            Account::query()
                ->whereKey(
                    $validated['account_id']
                )
                ->where('is_active', true)
                ->where(
                    'type',
                    '!=',
                    Account::TYPE_FIXED_DEPOSIT
                )
                ->firstOrFail();

        $validated['shareholder_id'] =
            $shareholder->id;

        $validated['account_id'] =
            $account->id;

        $validated['created_by'] =
            $request->user()->id;

        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment =
                $request
                    ->file('attachment')
                    ->store(
                        'share-transactions/attachments',
                        'local'
                    );

            $validated['attachment'] =
                $attachment;
        }

        try {
            $transaction =
                $this
                    ->shareTransactionService
                    ->create(
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
            ->route(
                'share-transactions.show',
                $transaction
            )
            ->with(
                'success',
                'Share transaction created successfully.'
            );
    }

    private function storeTransferInternal(
        Request $request
    ) {
        $this->ensureCanCreateTransfer(
            $request
        );

        $user = $request->user();

        $rules = [
            'transaction_type' => [
                'required',
                'in:transfer',
            ],

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
                'nullable',
                'string',
                'max:10',
            ],

            'financial_year' => [
                'nullable',
                'string',
                'max:10',
            ],

            'kitta' => [
                'required',
                'integer',
                'min:1',
            ],

            'per_kitta_value' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:0.01',
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
                'max:2000',
            ],
        ];

        if (! $user->isShareholder()) {
            $rules['shareholder_id'] = [
                'required',
                'integer',
                'exists:shareholders,id',
            ];
        }

        $validated =
            $request->validate($rules);

        $validated = array_replace(
            $validated,
            app(FinancialDateService::class)
                ->fromEnglishDate(
                    $validated['date_ad']
                )
        );

        if ($user->isShareholder()) {
            $fromShareholder =
                $user
                    ->shareholder()
                    ->where('is_active', true)
                    ->firstOrFail();

            $validated['shareholder_id'] =
                $fromShareholder->id;
        }

        $validated['created_by'] =
            $user->id;

        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment =
                $request
                    ->file('attachment')
                    ->store(
                        'share-transfers/attachments',
                        'local'
                    );

            $validated['attachment'] =
                $attachment;
        }

        try {
            $transaction =
                $this
                    ->shareTransferService
                    ->create(
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
            ->route(
                'share-transactions.show',
                $transaction
            )
            ->with(
                'success',
                'Share transferred successfully.'
            );
    }

    /*
     * Old transfer URLs are kept temporarily.
     */

    public function createTransfer(
        Request $request
    ) {
        $this->ensureCanCreateTransfer(
            $request
        );

        return redirect()
            ->route(
                'share-transactions.create',
                ['type' => 'transfer']
            );
    }

    public function storeTransfer(
        Request $request
    ) {
        $request->merge([
            'transaction_type' =>
                ShareTransaction::TYPE_TRANSFER,
        ]);

        return $this->store($request);
    }

    public function show(
        Request $request,
        ShareTransaction $shareTransaction
    ) {
        $this->ensureCanViewTransaction(
            $request,
            $shareTransaction
        );

        $shareTransaction->load([
            'shareholder',
            'toShareholder',
            'account',
            'creator',
            'canceller',
        ]);

        return view(
            'share-transactions.show',
            compact('shareTransaction')
        );
    }

    public function edit(
    Request $request,
    ShareTransaction $shareTransaction
) {
    $this->ensureCanEditTransaction(
        $request,
        $shareTransaction
    );

    abort_unless(
        $shareTransaction->status === 'active',
        403,
        'Only active transactions can be edited.'
    );

    $shareTransaction->load([
        'shareholder',
        'toShareholder',
        'account',
        'creator',
    ]);

    return view(
        'share-transactions.edit',
        compact('shareTransaction')
    );
}
    public function update(
        Request $request,
        ShareTransaction $shareTransaction
    ) {
        $this->ensureCanEditTransaction(
            $request,
            $shareTransaction
        );

        if (
            $shareTransaction->status !== 'active'
        ) {
            return back()
                ->withErrors([
                    'transaction' =>
                        'Only active transactions can be edited.',
                ]);
        }

        $validated =
            $request->validate([
                'date_ad' => [
                    'required',
                    'date',
                ],

                'note' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);

        $validated = array_replace(
            $validated,
            app(FinancialDateService::class)
                ->fromEnglishDate(
                    $validated['date_ad']
                )
        );

        try {
            if (
                $shareTransaction->transaction_type
                === ShareTransaction::TYPE_TRANSFER
            ) {
                $transaction =
                    $this
                        ->shareTransferService
                        ->updateMetadata(
                            $shareTransaction,
                            $validated
                        );
            } else {
                $transaction =
                    $this
                        ->shareTransactionService
                        ->updateMetadata(
                            $shareTransaction,
                            $validated
                        );
            }
        } catch (\RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'transaction' =>
                        $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route(
                'share-transactions.show',
                $transaction
            )
            ->with(
                'success',
                'Share transaction updated successfully.'
            );
    }

    public function cancel(
    Request $request,
    ShareTransaction $shareTransaction
) {
    $user = $request->user();

    abort_unless($user, 403);

    if (
        $shareTransaction->transaction_type
        === ShareTransaction::TYPE_TRANSFER
    ) {
        abort_unless(
            $user->isAdmin()
            || (
                $user->isStaff()
                && $user->hasPermission(
                    'share-transfer.create'
                )
            )
            || (
                $user->isShareholder()
                && $user
                    ->shareholder()
                    ->where('is_active', true)
                    ->where(
                        function ($query) use ($shareTransaction) {
                            $query
                                ->whereKey(
                                    $shareTransaction->shareholder_id
                                )
                                ->orWhereKey(
                                    $shareTransaction->to_shareholder_id
                                );
                        }
                    )
                    ->exists()
            ),
            403
        );
    } else {
        abort_unless(
            $user->isAdmin()
            || (
                $user->isStaff()
                && $user->hasPermission(
                    'share-transaction.cancel'
                )
            ),
            403
        );
    }

    $validated =
        $request->validate([
            'cancellation_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

    try {
        if (
            $shareTransaction->transaction_type
            === ShareTransaction::TYPE_TRANSFER
        ) {
            $transaction =
                $this
                    ->shareTransferService
                    ->cancel(
                        $shareTransaction,
                        $user->id,
                        $validated['cancellation_reason']
                    );
        } else {
            $transaction =
                $this
                    ->shareTransactionService
                    ->cancel(
                        $shareTransaction,
                        $user->id,
                        $validated['cancellation_reason']
                    );
        }
    } catch (\RuntimeException $exception) {
        return back()
            ->withInput()
            ->withErrors([
                'transaction' =>
                    $exception->getMessage(),
            ]);
    }

    return redirect()
        ->route(
            'share-transactions.show',
            $transaction
        )
        ->with(
            'success',
            'Share transaction cancelled successfully.'
        );
}
    public function convertDate(
        Request $request
    ) {
        $user = $request->user();

        abort_unless(
            $user
            && (
                $user->isAdmin()
                || (
                    $user->isStaff()
                    && (
                        $user->hasPermission(
                            'share-transaction.create'
                        )
                        || $user->hasPermission(
                            'share-transfer.create'
                        )
                    )
                )
                || (
                    $user->isShareholder()
                    && $user
                        ->shareholder()
                        ->where(
                            'is_active',
                            true
                        )
                        ->exists()
                )
            ),
            403
        );

        $validated =
            $request->validate([
                'date_ad' => [
                    'required',
                    'date',
                ],
            ]);

        return response()->json(
            app(FinancialDateService::class)
                ->fromEnglishDate(
                    $validated['date_ad']
                )
        );
    }

    private function canCreateShareTransaction(
        Request $request
    ): bool {
        $user = $request->user();

        return (bool) (
            $user
            && (
                $user->isAdmin()
                || (
                    $user->isStaff()
                    && $user->hasPermission(
                        'share-transaction.create'
                    )
                )
            )
        );
    }

    private function canCreateTransfer(
        Request $request
    ): bool {
        $user = $request->user();

        return (bool) (
            $user
            && (
                $user->isAdmin()
                || (
                    $user->isStaff()
                    && $user->hasPermission(
                        'share-transfer.create'
                    )
                )
                || (
                    $user->isShareholder()
                    && $user
                        ->shareholder()
                        ->where(
                            'is_active',
                            true
                        )
                        ->exists()
                )
            )
        );
    }

    private function ensureCanCreateShareTransaction(
        Request $request
    ): void {
        abort_unless(
            $this->canCreateShareTransaction(
                $request
            ),
            403
        );
    }

    private function ensureCanCreateTransfer(
        Request $request
    ): void {
        abort_unless(
            $this->canCreateTransfer(
                $request
            ),
            403
        );
    }

    private function ensureCanViewTransaction(
        Request $request,
        ShareTransaction $transaction
    ): void {
        $user = $request->user();

        abort_unless($user, 403);

        if (
            $transaction->transaction_type
            === ShareTransaction::TYPE_TRANSFER
        ) {
            if ($user->isAdmin() || $user->isHelpDesk()) {
                return;
            }

            if (
                $user->isStaff()
                && $user->hasPermission(
                    'share-transfer.view'
                )
            ) {
                return;
            }

            if ($user->isShareholder()) {
                $shareholder =
                    $user
                        ->shareholder()
                        ->firstOrFail();

                abort_unless(
                    (int) $transaction->shareholder_id
                        === (int) $shareholder->id
                    || (int) $transaction->to_shareholder_id
                        === (int) $shareholder->id,
                    403
                );

                return;
            }

            abort(403);
        }

        abort_unless(
            $user->isAdmin()
            || $user->isHelpDesk()
            || (
                $user->isStaff()
                && $user->hasPermission(
                    'share-transaction.view'
                )
            ),
            403
        );
    }

    private function ensureCanEditTransaction(
        Request $request,
        ShareTransaction $transaction
    ): void {
        $user = $request->user();

        abort_unless($user, 403);

        if (
            $transaction->transaction_type
            === ShareTransaction::TYPE_TRANSFER
        ) {
            if ($user->isAdmin()) {
                return;
            }

            if (
                $user->isStaff()
                && $user->hasPermission(
                    'share-transfer.create'
                )
            ) {
                return;
            }

            if ($user->isShareholder()) {
                $shareholder =
                    $user
                        ->shareholder()
                        ->where(
                            'is_active',
                            true
                        )
                        ->firstOrFail();

                abort_unless(
                    (int) $transaction->shareholder_id
                        === (int) $shareholder->id
                    || (int) $transaction->to_shareholder_id
                        === (int) $shareholder->id,
                    403
                );

                return;
            }

            abort(403);
        }

        abort_unless(
            $user->isAdmin()
            || (
                $user->isStaff()
                && $user->hasPermission(
                    'share-transaction.create'
                )
            ),
            403
        );
    }
}
