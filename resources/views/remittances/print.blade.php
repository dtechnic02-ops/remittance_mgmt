<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Filtered Remittances Report</title>
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
    <h1>Filtered Remittances Report</h1>
    <div class="meta">Generated: {{ now()->format('Y-m-d H:i') }} | Total Records: {{ number_format($transactions->count()) }}</div>
    <table>
        <thead><tr><th>Transaction</th><th>Type</th><th>Date AD</th><th>Date BS</th><th>FY</th><th>Customer</th><th>Provider / Account</th><th class="number">Principal</th><th class="number">Commission</th><th class="number">Customer Cash</th><th>Status</th></tr></thead>
        <tbody>
            @forelse ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_number }}</td>
                    <td>{{ strtoupper($transaction->direction) }}</td>
                    <td>{{ $transaction->date_ad?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $transaction->date_bs ?: '-' }}</td>
                    <td>{{ $transaction->financial_year ?: '-' }}</td>
                    <td>{{ $transaction->customer?->name ?? '-' }}</td>
                    <td>{{ $transaction->providerAccount?->name ?? '-' }} / {{ $transaction->cashAccount?->name ?? '-' }}</td>
                    <td class="number">Rs. {{ number_format($transaction->principal_amount) }}</td>
                    <td class="number">Rs. {{ number_format($transaction->service_charge) }}</td>
                    <td class="number">Rs. {{ number_format($transaction->total_cash_received) }}</td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="11" style="text-align:center">No filtered remittances found.</td></tr>
            @endforelse
        </tbody>
        <tfoot><tr><td colspan="8" class="number">Total Commission</td><td class="number">Rs. {{ number_format($totalCommission) }}</td><td colspan="2"></td></tr></tfoot>
    </table>
</body>
</html>
