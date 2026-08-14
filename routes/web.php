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
use App\Http\Controllers\ShareholderPortalController;
use App\Http\Controllers\PrivateFileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CustomerImportController;
use App\Http\Controllers\StaffDashboardController;
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
    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->name('admin.dashboard');

    Route::resource('accounts', AccountController::class);
    Route::get('/accounts/{account}/attachment', [PrivateFileController::class, 'account'])
        ->name('accounts.attachment');
Route::resource('opening-balances', OpeningBalanceController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('/opening-balances/{openingBalance}/attachment', [PrivateFileController::class, 'openingBalance'])
        ->name('opening-balances.attachment');
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
    Route::get('/staff/dashboard', StaffDashboardController::class)
        ->name('staff.dashboard');
});


Route::middleware(['auth', 'verified', 'shareholder'])->group(function () {
    Route::get(
        '/shareholder/dashboard',
        [ShareholderPortalController::class, 'dashboard']
    )->name('shareholder.dashboard');
    Route::get('/shareholder/documents/{shareholder}/{type}', [PrivateFileController::class, 'shareholder'])
        ->name('shareholder.documents.show');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/customers/date-convert', [CustomerController::class, 'convertDate'])
        ->name('customers.date-convert');
    Route::get('/customers/import', [CustomerImportController::class, 'create'])
        ->middleware('permission:customer.create')->name('customers.import.create');
    Route::post('/customers/import', [CustomerImportController::class, 'store'])
        ->middleware('permission:customer.create')->name('customers.import.store');
    Route::get('/customers/import/template', [CustomerImportController::class, 'template'])
        ->middleware('permission:customer.create')->name('customers.import.template');

    Route::get('/customers/{customer}/documents/type/{type}', [PrivateFileController::class, 'customer'])
        ->middleware('permission:customer.view')->name('customers.documents.type');
    Route::get('/customers/{customer}/documents/file/{document}', [PrivateFileController::class, 'customerDocument'])
        ->middleware('permission:customer.view')->name('customers.documents.show');
    Route::get('/shareholders/{shareholder}/documents/{type}', [PrivateFileController::class, 'shareholder'])
        ->middleware('permission:shareholder.view')->name('shareholders.documents.show');
    Route::get('/remittances/{remittance}/attachment', [PrivateFileController::class, 'remittance'])
        ->middleware('permission:remittance.view')->name('remittances.attachment');
    Route::get('/expenses/{expense}/attachment', [PrivateFileController::class, 'expense'])
        ->middleware('permission:expense.view')->name('expenses.attachment');
    Route::get('/incomes/{income}/attachment', [PrivateFileController::class, 'income'])
        ->middleware('permission:income.view')->name('incomes.attachment');
    Route::get('/share-transactions/{shareTransaction}/attachment', [PrivateFileController::class, 'shareTransaction'])
        ->middleware('permission:share-transaction.view')->name('share-transactions.attachment');
    Route::get('/borrowings/{borrowing}/attachment', [PrivateFileController::class, 'borrowing'])
        ->middleware('permission:borrowing.view')->name('borrowings.attachment');

    Route::resource('customers', CustomerController::class)
        ->only([
            'index',
            'create',
            'store',
            'show',
            'edit',
            'update',
            'destroy',
        ])
        ->middlewareFor(['index', 'show'], 'permission:customer.view')
        ->middlewareFor(['create', 'store'], 'permission:customer.create')
        ->middlewareFor(['edit', 'update'], 'permission:customer.update')
        ->middlewareFor('destroy', 'permission:customer.delete');

    Route::delete(
        '/customers/{customer}/documents/{document}',
        [CustomerController::class, 'destroyOtherDocument']
    )->middleware('permission:customer.update')
        ->name('customers.documents.destroy');
Route::get(
    '/remittances/date-convert',
    [RemittanceTransactionController::class, 'convertDate']
)->middleware('permission:remittance.create')->name('remittances.date-convert');
    Route::get(
    '/remittances',
    [RemittanceTransactionController::class, 'index']
)->middleware('permission:remittance.view')->name('remittances.index');

Route::get(
    '/remittances/create',
    [RemittanceTransactionController::class, 'create']
)->middleware('permission:remittance.create')->name('remittances.create');
Route::get(
    '/account-transfers',
    [AccountTransferController::class, 'index']
)->middleware('permission:account-transfer.view')->name('account-transfers.index');

Route::get(
    '/account-transfers/create',
    [AccountTransferController::class, 'create']
)->middleware('permission:account-transfer.create')->name('account-transfers.create');

Route::post(
    '/account-transfers',
    [AccountTransferController::class, 'store']
)->middleware('permission:account-transfer.create')->name('account-transfers.store');

Route::get(
    '/account-transfers/date-convert',
    [AccountTransferController::class, 'convertDate']
)->middleware('permission:account-transfer.create')->name('account-transfers.date-convert');

Route::post(
    '/account-transfers/{accountTransfer}/cancel',
    [AccountTransferController::class, 'cancel']
)->middleware('permission:account-transfer.cancel')->name('account-transfers.cancel');

Route::get(
    '/account-transfers/{accountTransfer}',
    [AccountTransferController::class, 'show']
)->middleware('permission:account-transfer.view')->name('account-transfers.show');
Route::post(
    '/remittances',
    [RemittanceTransactionController::class, 'store']
)->middleware('permission:remittance.create')->name('remittances.store');

Route::post(
    '/remittances/{remittance}/cancel',
    [RemittanceTransactionController::class, 'cancel']
)->middleware('permission:remittance.cancel')->name('remittances.cancel');

Route::get(
    '/remittances/{remittance}',
    [RemittanceTransactionController::class, 'show']
)->middleware('permission:remittance.view')->name('remittances.show');
Route::get(
    '/ledger',
    [LedgerController::class, 'index']
)->middleware('permission:ledger.view')->name('ledger.index');
Route::resource(
    'expense-categories',
    ExpenseCategoryController::class
)->only([
    'index',
    'create',
    'store',
    'edit',
    'update',
])
    ->middlewareFor('index', 'permission:expense-category.view')
    ->middlewareFor(['create', 'store', 'edit', 'update'], 'permission:expense-category.manage');
Route::get(
    '/expenses',
    [ExpenseController::class, 'index']
)->middleware('permission:expense.view')->name('expenses.index');

Route::get(
    '/expenses/create',
    [ExpenseController::class, 'create']
)->middleware('permission:expense.create')->name('expenses.create');

Route::post(
    '/expenses',
    [ExpenseController::class, 'store']
)->middleware('permission:expense.create')->name('expenses.store');

Route::get(
    '/expenses/date-convert',
    [ExpenseController::class, 'convertDate']
)->middleware('permission:expense.create')->name('expenses.date-convert');

Route::post(
    '/expenses/{expense}/cancel',
    [ExpenseController::class, 'cancel']
)->middleware('permission:expense.cancel')->name('expenses.cancel');

Route::get(
    '/expenses/{expense}',
    [ExpenseController::class, 'show']
)->middleware('permission:expense.view')->name('expenses.show');
Route::resource(
    'income-categories',
    IncomeCategoryController::class
)->only([
    'index',
    'create',
    'store',
    'edit',
    'update',
])
    ->middlewareFor('index', 'permission:income-category.view')
    ->middlewareFor(['create', 'store', 'edit', 'update'], 'permission:income-category.manage');
Route::get(
    '/incomes',
    [IncomeController::class, 'index']
)->middleware('permission:income.view')->name('incomes.index');

Route::get(
    '/incomes/create',
    [IncomeController::class, 'create']
)->middleware('permission:income.create')->name('incomes.create');

Route::post(
    '/incomes',
    [IncomeController::class, 'store']
)->middleware('permission:income.create')->name('incomes.store');

Route::get(
    '/incomes/date-convert',
    [IncomeController::class, 'convertDate']
)->middleware('permission:income.create')->name('incomes.date-convert');

Route::post(
    '/incomes/{income}/cancel',
    [IncomeController::class, 'cancel']
)->middleware('permission:income.cancel')->name('incomes.cancel');

Route::get(
    '/incomes/{income}',
    [IncomeController::class, 'show']
)->middleware('permission:income.view')->name('incomes.show');
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
])
    ->middlewareFor(['index', 'show'], 'permission:shareholder.view')
    ->middlewareFor(['create', 'store'], 'permission:shareholder.create')
    ->middlewareFor(['edit', 'update'], 'permission:shareholder.update');
Route::get(
    '/share-transactions/convert-date',
    [ShareTransactionController::class, 'convertDate']
)->middleware('permission:share-transaction.create')->name('share-transactions.convert-date');

Route::post(
    '/share-transactions/{shareTransaction}/cancel',
    [ShareTransactionController::class, 'cancel']
)->middleware('permission:share-transaction.cancel')->name('share-transactions.cancel');

Route::resource(
    'share-transactions',
    ShareTransactionController::class
)->only([
    'index',
    'create',
    'store',
    'show',
])
    ->middlewareFor(['index', 'show'], 'permission:share-transaction.view')
    ->middlewareFor(['create', 'store'], 'permission:share-transaction.create');
Route::resource(
    'lenders',
    LenderController::class
)->only([
    'index',
    'create',
    'store',
    'edit',
    'update',
])
    ->middlewareFor('index', 'permission:lender.view')
    ->middlewareFor(['create', 'store'], 'permission:lender.create')
    ->middlewareFor(['edit', 'update'], 'permission:lender.update');
Route::get(
    '/borrowings/convert-date',
    [BorrowingController::class, 'convertDate']
)->middleware('permission:borrowing.create')->name('borrowings.convert-date');

Route::post(
    '/borrowings/{borrowing}/cancel',
    [BorrowingController::class, 'cancel']
)->middleware('permission:borrowing.cancel')->name('borrowings.cancel');

Route::resource(
    'borrowings',
    BorrowingController::class
)->only([
    'index',
    'create',
    'store',
])
    ->middlewareFor('index', 'permission:borrowing.view')
    ->middlewareFor(['create', 'store'], 'permission:borrowing.create');


});

require __DIR__.'/auth.php';
