<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Filtered Share Transactions Report</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111827; font-family: Arial, sans-serif; font-size: 10px; }
        .toolbar { margin-bottom: 12px; text-align: right; }
        .toolbar button { border: 1px solid #9ca3af; border-radius: 4px; background: white; padding: 7px 14px; cursor: pointer; }
        h1 { margin: 0 0 4px; font-size: 19px; }
        .meta { margin-bottom: 12px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 5px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 700; }
        .number { text-align: right; white-space: nowrap; }
        tfoot td { font-weight: 700; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Print</button></div>
    <h1>Filtered Share Transactions Report</h1>
    <div class="meta">Generated: {{ now()->format('Y-m-d H:i') }} | Total Records: {{ number_format($transactions->count()) }}</div>
    <table>
        <thead><tr><th>Transaction</th><th>Type</th><th>Date AD</th><th>Date BS</th><th>FY</th><th>Shareholder / From</th><th>To / Account</th><th class="number">Kitta</th><th class="number">Per Kitta</th><th class="number">Amount / Value</th><th>Status</th></tr></thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_number }}</td>
                    <td>{{ ucfirst($transaction->transaction_type) }}</td>
                    <td>{{ $transaction->date_ad?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $transaction->date_bs ?: '-' }}</td>
                    <td>{{ $transaction->financial_year ?: '-' }}</td>
                    <td>{{ $transaction->shareholder?->name ?? '-' }}</td>
                    <td>{{ $transaction->transaction_type === 'transfer' ? ($transaction->toShareholder?->name ?? '-') : ($transaction->account?->name ?? '-') }}</td>
                    <td class="number">{{ number_format($transaction->kitta) }}</td>
                    <td class="number">{{ number_format($transaction->per_kitta_value) }}</td>
                    <td class="number">Rs. {{ number_format($transaction->total_amount) }}</td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="11" style="text-align:center">No filtered share transactions found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr><td colspan="7" class="number">Total Kitta</td><td class="number">{{ number_format($totalKitta) }}</td><td colspan="3"></td></tr>
            <tr><td colspan="9" class="number">Total Amount / Value</td><td class="number">Rs. {{ $totalAmount > 0 ? '+' : '' }}{{ number_format($totalAmount) }}</td><td></td></tr>
        </tfoot>
    </table>
</body>
</html>
