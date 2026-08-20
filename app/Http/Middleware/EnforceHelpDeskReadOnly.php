<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceHelpDeskReadOnly
{
    private const ALLOWED_ROUTES = [
        'dashboard',
        'help-desk.dashboard',
        'customers.index',
        'customers.show',
        'customers.documents.type',
        'customers.documents.show',
        'remittances.index',
        'remittances.show',
        'remittances.attachment',
        'accounts.index',
        'accounts.show',
        'accounts.attachment',
        'account-transfers.index',
        'account-transfers.show',
        'ledger.index',
        'incomes.index',
        'incomes.show',
        'incomes.attachment',
        'expenses.index',
        'expenses.show',
        'expenses.attachment',
        'lenders.index',
        'lenders.show',
        'borrowings.index',
        'borrowings.attachment',
        'shareholders.index',
        'shareholders.show',
        'shareholders.documents.show',
        'share-transactions.index',
        'share-transactions.show',
        'share-transactions.attachment',
        'share-transfers.attachment',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->isHelpDesk()) {
            return $next($request);
        }

        if ($request->routeIs('logout')) {
            return $next($request);
        }

        abort_unless(
            in_array($request->method(), ['GET', 'HEAD'], true)
            && in_array($request->route()?->getName(), self::ALLOWED_ROUTES, true),
            403
        );

        return $next($request);
    }
}
