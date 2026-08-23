<?php

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RemittanceExportService
{
    public function excel(Collection $transactions): BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Remittances');
        $headers = ['Transaction', 'Type', 'Date AD', 'Date BS', 'Financial Year', 'Customer', 'Provider', 'Account', 'Principal', 'Commission', 'Customer Cash', 'Status', 'Provider Reference'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValueExplicit([$index + 1, 1], $header, DataType::TYPE_STRING);
        }

        foreach ($transactions as $rowIndex => $transaction) {
            $row = $rowIndex + 2;
            $values = [
                $transaction->transaction_number,
                strtoupper($transaction->direction),
                $transaction->date_ad?->format('Y-m-d') ?? '',
                $transaction->date_bs ?? '',
                $transaction->financial_year ?? '',
                $transaction->customer?->name ?? '',
                $transaction->providerAccount?->name ?? '',
                $transaction->cashAccount?->name ?? '',
                $transaction->principal_amount,
                $transaction->service_charge,
                $transaction->total_cash_received,
                ucfirst($transaction->status),
                $transaction->provider_reference ?? '',
            ];

            foreach ($values as $columnIndex => $value) {
                if (in_array($columnIndex, [8, 9, 10], true)) {
                    $sheet->setCellValue([$columnIndex + 1, $row], $value);
                } else {
                    $sheet->setCellValueExplicit([$columnIndex + 1, $row], (string) $value, DataType::TYPE_STRING);
                }
            }
        }

        $lastRow = $transactions->count() + 2;
        $sheet->setCellValue("I{$lastRow}", 'Total Commission');
        $sheet->setCellValue("J{$lastRow}", $transactions->sum('service_charge'));
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->getStyle("I{$lastRow}:J{$lastRow}")->getFont()->setBold(true);
        $sheet->freezePane('A2');
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle("I2:K{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        $path = tempnam(sys_get_temp_dir(), 'remittances-export-');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return response()->download($path, 'remittances-filtered-'.now()->format('Y-m-d').'.xlsx')
            ->deleteFileAfterSend(true);
    }
}
