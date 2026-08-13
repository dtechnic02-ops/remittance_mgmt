<?php

namespace App\Http\Controllers;

use App\Models\ShareTransaction;
use Illuminate\Http\Request;

class ShareholderPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $shareholder = $request->user()
            ->shareholder()
            ->first();

        $transactions = $shareholder
            ? ShareTransaction::query()
                ->where('shareholder_id', $shareholder->id)
                ->orderByDesc('date_ad')
                ->orderByDesc('id')
                ->paginate(20)
            : null;

        return view('shareholder.dashboard', compact(
            'shareholder',
            'transactions'
        ));
    }
}
