<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111827; font-family: Arial, sans-serif; font-size: 11px; }
        .toolbar { margin-bottom: 12px; text-align: right; }
        .toolbar button { border: 1px solid #9ca3af; border-radius: 4px; background: white; padding: 7px 14px; cursor: pointer; }
        h1 { margin: 0 0 4px; font-size: 20px; }
        .meta { margin-bottom: 14px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 700; }
        .number { text-align: right; white-space: nowrap; }
        tfoot td { font-weight: 700; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Print</button></div>
    <h1>{{ $title }}</h1>
    <div class="meta">Generated: {{ now()->format('Y-m-d H:i') }} | Total Records: {{ number_format($transactions->count()) }}</div>
    <table>
        <thead>
            <tr>
                <th>Number</th><th>Date AD</th><th>Date BS</th><th>FY</th><th>Category</th><th>Account</th><th class="number">Amount</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $type === 'income' ? $transaction->income_number : $transaction->expense_number }}</td>
                    <td>{{ $transaction->date_ad?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $transaction->date_bs ?: '-' }}</td>
                    <td>{{ $transaction->financial_year ?: '-' }}</td>
                    <td>{{ $transaction->category?->name ?? '-' }}</td>
                    <td>{{ $transaction->account?->name ?? '-' }}</td>
                    <td class="number">Rs. {{ number_format($transaction->amount) }}</td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center">No filtered records found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr><td colspan="6" class="number">Total Amount</td><td class="number">Rs. {{ number_format($totalAmount) }}</td><td></td></tr>
        </tfoot>
    </table>
</body>
</html>
