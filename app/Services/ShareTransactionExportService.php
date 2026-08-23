<?php

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ShareTransactionExportService
{
    public function excel(Collection $transactions): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Share Transactions');
        $headers = ['Transaction', 'Type', 'Date AD', 'Date BS', 'Financial Year', 'Shareholder / From', 'To / Account', 'Kitta', 'Per Kitta', 'Amount / Value', 'Status', 'Reference'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValueExplicit([$index + 1, 1], $header, DataType::TYPE_STRING);
        }

        foreach ($transactions as $rowIndex => $transaction) {
            $row = $rowIndex + 2;
            $toOrAccount = $transaction->transaction_type === 'transfer'
                ? $transaction->toShareholder?->name
                : $transaction->account?->name;
            $values = [
                $transaction->transaction_number,
                ucfirst($transaction->transaction_type),
                $transaction->date_ad?->format('Y-m-d') ?? '',
                $transaction->date_bs ?? '',
                $transaction->financial_year ?? '',
                $transaction->shareholder?->name ?? '',
                $toOrAccount ?? '',
                $transaction->kitta,
                $transaction->per_kitta_value,
                $transaction->total_amount,
                ucfirst($transaction->status),
                $transaction->reference ?? '',
            ];

            foreach ($values as $columnIndex => $value) {
                if (in_array($columnIndex, [7, 8, 9], true)) {
                    $sheet->setCellValue([$columnIndex + 1, $row], $value);
                } else {
                    $sheet->setCellValueExplicit([$columnIndex + 1, $row], (string) $value, DataType::TYPE_STRING);
                }
            }
        }

        $lastRow = $transactions->count() + 2;
        $sheet->setCellValue("I{$lastRow}", 'Total');
        $sheet->setCellValue("J{$lastRow}", $transactions->sum('total_amount'));
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle("I{$lastRow}:J{$lastRow}")->getFont()->setBold(true);
        $sheet->freezePane('A2');
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle("H2:J{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        $path = tempnam(sys_get_temp_dir(), 'share-transactions-export-');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return response()->download($path, 'share-transactions-filtered-'.now()->format('Y-m-d').'.xlsx')
            ->deleteFileAfterSend(true);
    }
}
