<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PROFIT LEDGER</title>
    <style>
        @page { size: A4 portrait; margin: 16mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111827; font-family: Arial, sans-serif; font-size: 12px; }
        .toolbar { margin-bottom: 12px; text-align: right; }
        .toolbar button { border: 1px solid #9ca3af; border-radius: 4px; background: white; padding: 7px 14px; cursor: pointer; }
        .company { margin: 0 0 4px; font-size: 16px; font-weight: 700; }
        h1 { margin: 0 0 10px; font-size: 20px; letter-spacing: 0.04em; }
        .meta { margin-bottom: 16px; color: #4b5563; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 8px 10px; }
        th { background: #f3f4f6; font-weight: 700; text-align: left; }
        .number { text-align: right; white-space: nowrap; }
        tfoot td { font-weight: 700; }
        .loss { color: #b91c1c; }
        .formula { margin-top: 16px; color: #4b5563; }
        @media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar"><button type="button" onclick="window.print()">Print</button></div>

    <p class="company">{{ $companyName }}</p>
    <h1>PROFIT LEDGER</h1>
    <div class="meta">
        Start Date: {{ $dateFrom !== '' ? $dateFrom : 'All dates' }}<br>
        End Date: {{ $dateTo !== '' ? $dateTo : 'All dates' }}<br>
        Printed: {{ $printedAt }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Particulars</th>
                <th class="number">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Income</td>
                <td class="number">{{ number_format($income) }}</td>
            </tr>
            <tr>
                <td>Remittance Charge</td>
                <td class="number">{{ number_format($remittanceCharge) }}</td>
            </tr>
            <tr>
                <td>Total Expenses</td>
                <td class="number">({{ number_format($expense) }})</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="{{ $profit < 0 ? 'loss' : '' }}">
                    {{ $profit < 0 ? 'TOTAL LOSS' : 'TOTAL PROFIT' }}
                </td>
                <td class="number {{ $profit < 0 ? 'loss' : '' }}">
                    {{ ($profit < 0 ? '-' : '').number_format(abs($profit)) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <p class="formula">Formula: Income + Remittance Charge - Expenses</p>
</body>
</html>
