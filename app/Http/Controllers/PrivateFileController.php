<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Borrowing;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Income;
use App\Models\OpeningBalance;
use App\Models\RemittanceTransaction;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateFileController extends Controller
{
    public function customer(Customer $customer, string $type): StreamedResponse
    {
        $path = match ($type) {
            'photo' => $customer->photo,
            'citizenship-front' => $customer->citizenship_front,
            'citizenship-back' => $customer->citizenship_back,
            default => abort(404),
        };

        return $this->serve($path);
    }

    public function customerDocument(Customer $customer, int $document): StreamedResponse
    {
        $record = $customer->otherDocuments()->whereKey($document)->firstOrFail();

        return $this->serve($record->file_path);
    }

    public function shareholder(Request $request, Shareholder $shareholder, string $type): StreamedResponse
    {
        if ($request->user()->isShareholder()) {
            abort_unless($request->user()->shareholder?->is($shareholder), 403);
        }

        $path = match ($type) {
            'photo' => $shareholder->photo,
            'citizenship-front' => $shareholder->citizenship_front,
            'citizenship-back' => $shareholder->citizenship_back,
            'other-document' => $shareholder->other_document,
            default => abort(404),
        };

        return $this->serve($path);
    }

    public function account(Account $account): StreamedResponse
    {
        return $this->serve($account->attachment);
    }

    public function openingBalance(OpeningBalance $openingBalance): StreamedResponse
    {
        return $this->serve($openingBalance->attachment);
    }

    public function remittance(RemittanceTransaction $remittance): StreamedResponse
    {
        return $this->serve($remittance->attachment);
    }

    public function expense(Expense $expense): StreamedResponse
    {
        return $this->serve($expense->attachment);
    }

    public function income(Income $income): StreamedResponse
    {
        return $this->serve($income->attachment);
    }

    public function shareTransaction(ShareTransaction $shareTransaction): StreamedResponse
    {
        return $this->serve($shareTransaction->attachment);
    }

    public function shareTransfer(Request $request, ShareTransaction $shareTransfer): StreamedResponse
    {
        abort_unless(
            $shareTransfer->transaction_type === ShareTransaction::TYPE_TRANSFER,
            404
        );

        $user = $request->user();
        $authorized = $user->isAdmin()
            || $user->isHelpDesk()
            || ($user->isStaff() && $user->hasPermission('share-transfer.view'));

        if ($user->isShareholder()) {
            $shareholderId = $user->shareholder?->id;
            $authorized = $shareholderId && (
                (int) $shareTransfer->shareholder_id === (int) $shareholderId
                || (int) $shareTransfer->to_shareholder_id === (int) $shareholderId
            );
        }

        abort_unless($authorized, 403);

        return $this->serve($shareTransfer->attachment);
    }

    public function borrowing(Borrowing $borrowing): StreamedResponse
    {
        return $this->serve($borrowing->attachment);
    }

    private function serve(?string $path): StreamedResponse
    {
        abort_if(blank($path) || ! Storage::disk('local')->exists($path), 404);

        $mime = Storage::disk('local')->mimeType($path) ?: 'application/octet-stream';
        $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
        abort_unless(in_array($mime, $allowed, true), 404);

        return Storage::disk('local')->response($path, null, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }
}
