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
use App\Http\Controllers\HelpDeskDashboardController;
use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\CompanyLinkController;
use App\Http\Controllers\PublicPageController;

use App\Http\Controllers\ShareholderAccountController;
use App\Http\Controllers\UserController;

Route::get('/', [PublicPageController::class, 'index'])
    ->name('home');



Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->isStaff()) {
        return redirect()->route('staff.dashboard');
    }

    if ($user->isHelpDesk()) {
        return redirect()->route('help-desk.dashboard');
    }

    if ($user->isShareholder()) {
        return redirect()->route('shareholder.dashboard');
    }

    abort(403);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboardController::class)
        ->name('admin.dashboard');

    Route::resource('accounts', AccountController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
Route::resource('opening-balances', OpeningBalanceController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('/opening-balances/{openingBalance}/attachment', [PrivateFileController::class, 'openingBalance'])
        ->name('opening-balances.attachment');
    Route::get(
    '/staff-permissions',
    [StaffPermissionController::class, 'index']
)->name('staff-permissions.index');
Route::get(
    '/admin/users',
    [UserController::class, 'index']
)->name('users.index');

Route::get('/admin/users/create', [UserController::class, 'create'])
    ->name('users.create');
Route::post('/admin/users', [UserController::class, 'store'])
    ->name('users.store');
Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])
    ->name('users.edit');
Route::put('/admin/users/{user}', [UserController::class, 'update'])
    ->name('users.update');

Route::post(
    '/admin/users/{user}/block',
    [UserController::class, 'block']
)->name('users.block');

Route::post(
    '/admin/users/{user}/unblock',
    [UserController::class, 'unblock']
)->name('users.unblock');
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
    Route::get('/admin/company-info', [CompanyInfoController::class, 'edit'])
    ->name('company-info.edit');

Route::put('/admin/company-info', [CompanyInfoController::class, 'update'])
    ->name('company-info.update');

    Route::get('/admin/company-links', [CompanyLinkController::class, 'index'])
    ->name('company-links.index');

Route::post('/admin/company-links', [CompanyLinkController::class, 'store'])
    ->name('company-links.store');

Route::put('/admin/company-links/{companyLink}', [CompanyLinkController::class, 'update'])
    ->name('company-links.update');

Route::delete('/admin/company-links/{companyLink}', [CompanyLinkController::class, 'destroy'])
    ->name('company-links.destroy');
    Route::get(
    '/admin/shareholders/{shareholder}/login-account',
    [ShareholderAccountController::class, 'edit']
)->name('shareholder-accounts.edit');

Route::put(
    '/admin/shareholders/{shareholder}/login-account',
    [ShareholderAccountController::class, 'update']
)->name('shareholder-accounts.update');

Route::post(
    '/admin/shareholders/{shareholder}/login-account/deactivate',
    [ShareholderAccountController::class, 'deactivate']
)->name('shareholder-accounts.deactivate');
});

Route::middleware(['auth', 'verified', 'admin-or-help-desk'])->group(function () {
    Route::get('/accounts', [AccountController::class, 'index'])
        ->name('accounts.index');
    Route::get('/accounts/{account}', [AccountController::class, 'show'])
        ->name('accounts.show');
    Route::get('/accounts/{account}/attachment', [PrivateFileController::class, 'account'])
        ->name('accounts.attachment');
});


Route::middleware(['auth', 'verified', 'staff'])->group(function () {
    Route::get('/staff/dashboard', StaffDashboardController::class)
        ->name('staff.dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/help-desk/dashboard', HelpDeskDashboardController::class)
        ->name('help-desk.dashboard');
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

    Route::post('/customers/{customer}/cancel', [CustomerController::class, 'cancel'])
        ->middleware('permission:customer.update')->name('customers.cancel');
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

Route::get(
    '/account-transfers/{accountTransfer}/edit',
    [AccountTransferController::class, 'edit']
)->middleware('permission:account-transfer.create')->name('account-transfers.edit');

Route::put(
    '/account-transfers/{accountTransfer}',
    [AccountTransferController::class, 'update']
)->middleware('permission:account-transfer.create')->name('account-transfers.update');

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

Route::get(
    '/remittances/{remittance}/edit',
    [RemittanceTransactionController::class, 'edit']
)->middleware('permission:remittance.create')->name('remittances.edit');

Route::put(
    '/remittances/{remittance}',
    [RemittanceTransactionController::class, 'update']
)->middleware('permission:remittance.create')->name('remittances.update');

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

Route::get(
    '/expenses/{expense}/edit',
    [ExpenseController::class, 'edit']
)->middleware('permission:expense.create')->name('expenses.edit');

Route::put(
    '/expenses/{expense}',
    [ExpenseController::class, 'update']
)->middleware('permission:expense.create')->name('expenses.update');

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

Route::get(
    '/incomes/{income}/edit',
    [IncomeController::class, 'edit']
)->middleware('permission:income.create')->name('incomes.edit');

Route::put(
    '/incomes/{income}',
    [IncomeController::class, 'update']
)->middleware('permission:income.create')->name('incomes.update');

Route::post(
    '/incomes/{income}/cancel',
    [IncomeController::class, 'cancel']
)->middleware('permission:income.cancel')->name('incomes.cancel');

Route::get(
    '/incomes/{income}',
    [IncomeController::class, 'show']
)->middleware('permission:income.view')->name('incomes.show');


Route::post(
    '/shareholders/{shareholder}/cancel',
    [ShareholderController::class, 'cancel']
)
    ->middleware('permission:shareholder.update')
    ->name('shareholders.cancel');

Route::get('/shareholders/import', [ShareholderController::class, 'importCreate'])
    ->middleware('permission:shareholder.create')
    ->name('shareholders.import.create');

Route::post('/shareholders/import', [ShareholderController::class, 'importStore'])
    ->middleware('permission:shareholder.create')
    ->name('shareholders.import.store');

Route::get('/shareholders/import/template', [ShareholderController::class, 'importTemplate'])
    ->middleware('permission:shareholder.create')
    ->name('shareholders.import.template');

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
    'destroy',
])
    ->middlewareFor(
        ['index', 'show'],
        'permission:shareholder.view'
    )
    ->middlewareFor(
        ['create', 'store'],
        'permission:shareholder.create'
    )
    ->middlewareFor(
        ['edit', 'update', 'destroy'],
        'permission:shareholder.update'
    );
 
Route::get(
    '/share-transactions/convert-date',
    [ShareTransactionController::class, 'convertDate']
)->name('share-transactions.convert-date');


/*
|--------------------------------------------------------------------------
| Unified Share Transactions
|--------------------------------------------------------------------------
|
| BUY / WITHDRAW / TRANSFER
| एउटै ShareTransactionController बाट handle हुन्छ।
|
*/

Route::get(
    '/share-transactions',
    [ShareTransactionController::class, 'index']
)->name('share-transactions.index');


/*
|--------------------------------------------------------------------------
| BUY / WITHDRAW
|--------------------------------------------------------------------------
*/

Route::get(
    '/share-transactions/create',
    [ShareTransactionController::class, 'create']
)->name('share-transactions.create');

Route::post(
    '/share-transactions',
    [ShareTransactionController::class, 'store']
)->name('share-transactions.store');


/*
|--------------------------------------------------------------------------
| SHARE TRANSFER CREATE
|--------------------------------------------------------------------------
|
| पुरानो URL राखिएको छ ताकि existing links नटुटून्।
| Controller चाहिँ अब एउटै हो।
|
*/

Route::get(
    '/share-transfers',
    function () {
        return redirect()->route(
            'share-transactions.index',
            ['type' => 'transfer']
        );
    }
)->name('share-transfers.index');

Route::get(
    '/share-transfers/create',
    [ShareTransactionController::class, 'createTransfer']
)->name('share-transfers.create');

Route::post(
    '/share-transfers',
    [ShareTransactionController::class, 'storeTransfer']
)->name('share-transfers.store');

Route::get(
    '/share-transfers/convert-date',
    [ShareTransactionController::class, 'convertDate']
)->name('share-transfers.convert-date');


/*
|--------------------------------------------------------------------------
| Attachments
|--------------------------------------------------------------------------
*/

Route::get(
    '/share-transfers/{shareTransfer}/attachment',
    [PrivateFileController::class, 'shareTransfer']
)->name('share-transfers.attachment');


/*
|--------------------------------------------------------------------------
| Unified View / Edit / Update
|--------------------------------------------------------------------------
*/

Route::get(
    '/share-transactions/{shareTransaction}/edit',
    [ShareTransactionController::class, 'edit']
)->name('share-transactions.edit');

Route::put(
    '/share-transactions/{shareTransaction}',
    [ShareTransactionController::class, 'update']
)->name('share-transactions.update');

Route::get(
    '/share-transactions/{shareTransaction}',
    [ShareTransactionController::class, 'show']
)->name('share-transactions.show');


/*
|--------------------------------------------------------------------------
| Cancel
|--------------------------------------------------------------------------
|
| BUY/WITHDRAW मात्र अहिले cancel हुन्छ।
| TRANSFER cancel Service तयार भएपछि यही route बाट handle हुनेछ।
|
*/

Route::post(
    '/share-transactions/{shareTransaction}/cancel',
    [ShareTransactionController::class, 'cancel']
)->name('share-transactions.cancel');


Route::resource(
    'lenders',
    LenderController::class
)->only([
    'index',
    'create',
    'store',
    'show',
    'edit',
    'update',
])
    ->middlewareFor(['index', 'show'], 'permission:lender.view')
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
