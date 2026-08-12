<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OpeningBalanceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RemittanceTransactionController;
use App\Http\Controllers\StaffPermissionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AccountTransferController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeCategoryController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ShareholderController;
use App\Http\Controllers\ShareTransactionController;
use App\Http\Controllers\LenderController;
use App\Http\Controllers\BorrowingController;
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->isStaff()) {
        return redirect()->route('staff.dashboard');
    }

    if ($user->isShareholder()) {
        return redirect()->route('shareholder.dashboard');
    }

    abort(403);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('accounts', AccountController::class);
Route::resource('opening-balances', OpeningBalanceController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get(
    '/staff-permissions',
    [StaffPermissionController::class, 'index']
)->name('staff-permissions.index');

Route::get(
    '/staff-permissions/{user}/edit',
    [StaffPermissionController::class, 'edit']
)->name('staff-permissions.edit');

Route::put(
    '/staff-permissions/{user}',
    [StaffPermissionController::class, 'update']
)->name('staff-permissions.update');
Route::resource('staff', StaffController::class)
    ->only([
        'index',
        'create',
        'store',
        'edit',
        'update',
        'destroy',
    ]);
});

Route::middleware(['auth', 'verified', 'staff'])->group(function () {
    Route::get('/staff/dashboard', function () {
        return view('staff.dashboard');
    })->name('staff.dashboard');
});


Route::middleware(['auth', 'verified', 'shareholder'])->group(function () {
    Route::get('/shareholder/dashboard', function () {
        return view('shareholder.dashboard');
    })->name('shareholder.dashboard');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('customers', CustomerController::class)
        ->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
            'destroy',
        ]);

    Route::delete(
        '/customers/{customer}/documents/{document}',
        [CustomerController::class, 'destroyOtherDocument']
    )->name('customers.documents.destroy');
Route::get(
    '/remittances/date-convert',
    [RemittanceTransactionController::class, 'convertDate']
)->name('remittances.date-convert');
    Route::get(
    '/remittances',
    [RemittanceTransactionController::class, 'index']
)->name('remittances.index');

Route::get(
    '/remittances/create',
    [RemittanceTransactionController::class, 'create']
)->name('remittances.create');
Route::get(
    '/account-transfers',
    [AccountTransferController::class, 'index']
)->name('account-transfers.index');

Route::get(
    '/account-transfers/create',
    [AccountTransferController::class, 'create']
)->name('account-transfers.create');

Route::post(
    '/account-transfers',
    [AccountTransferController::class, 'store']
)->name('account-transfers.store');

Route::get(
    '/account-transfers/date-convert',
    [AccountTransferController::class, 'convertDate']
)->name('account-transfers.date-convert');

Route::post(
    '/account-transfers/{accountTransfer}/cancel',
    [AccountTransferController::class, 'cancel']
)->name('account-transfers.cancel');

Route::get(
    '/account-transfers/{accountTransfer}',
    [AccountTransferController::class, 'show']
)->name('account-transfers.show');
Route::post(
    '/remittances',
    [RemittanceTransactionController::class, 'store']
)->name('remittances.store');

Route::post(
    '/remittances/{remittance}/cancel',
    [RemittanceTransactionController::class, 'cancel']
)->name('remittances.cancel');

Route::get(
    '/remittances/{remittance}',
    [RemittanceTransactionController::class, 'show']
)->name('remittances.show');
Route::get(
    '/ledger',
    [LedgerController::class, 'index']
)->name('ledger.index');
Route::resource(
    'expense-categories',
    ExpenseCategoryController::class
)->only([
    'index',
    'create',
    'store',
    'edit',
    'update',
]);
Route::get(
    '/expenses',
    [ExpenseController::class, 'index']
)->name('expenses.index');

Route::get(
    '/expenses/create',
    [ExpenseController::class, 'create']
)->name('expenses.create');

Route::post(
    '/expenses',
    [ExpenseController::class, 'store']
)->name('expenses.store');

Route::get(
    '/expenses/date-convert',
    [ExpenseController::class, 'convertDate']
)->name('expenses.date-convert');

Route::post(
    '/expenses/{expense}/cancel',
    [ExpenseController::class, 'cancel']
)->name('expenses.cancel');

Route::get(
    '/expenses/{expense}',
    [ExpenseController::class, 'show']
)->name('expenses.show');
Route::resource(
    'income-categories',
    IncomeCategoryController::class
)->only([
    'index',
    'create',
    'store',
    'edit',
    'update',
]);
Route::get(
    '/incomes',
    [IncomeController::class, 'index']
)->name('incomes.index');

Route::get(
    '/incomes/create',
    [IncomeController::class, 'create']
)->name('incomes.create');

Route::post(
    '/incomes',
    [IncomeController::class, 'store']
)->name('incomes.store');

Route::get(
    '/incomes/date-convert',
    [IncomeController::class, 'convertDate']
)->name('incomes.date-convert');

Route::post(
    '/incomes/{income}/cancel',
    [IncomeController::class, 'cancel']
)->name('incomes.cancel');

Route::get(
    '/incomes/{income}',
    [IncomeController::class, 'show']
)->name('incomes.show');
Route::resource(
    'shareholders',
    ShareholderController::class
)->only([
    'index',
    'create',
    'store',
    'show',
    'edit',
    'update',
]);
Route::get(
    '/share-transactions/convert-date',
    [ShareTransactionController::class, 'convertDate']
)->name('share-transactions.convert-date');

Route::post(
    '/share-transactions/{shareTransaction}/cancel',
    [ShareTransactionController::class, 'cancel']
)->name('share-transactions.cancel');

Route::resource(
    'share-transactions',
    ShareTransactionController::class
)->only([
    'index',
    'create',
    'store',
    'show',
]);
Route::resource(
    'lenders',
    LenderController::class
)->only([
    'index',
    'create',
    'store',
    'edit',
    'update',
]);
Route::get(
    '/borrowings/convert-date',
    [BorrowingController::class, 'convertDate']
)->name('borrowings.convert-date');

Route::post(
    '/borrowings/{borrowing}/cancel',
    [BorrowingController::class, 'cancel']
)->name('borrowings.cancel');

Route::resource(
    'borrowings',
    BorrowingController::class
)->only([
    'index',
    'create',
    'store',
]);


});

require __DIR__.'/auth.php';