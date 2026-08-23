<?php

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IncomeExpenseExportService
{
    public function excel(Collection $transactions, string $type): BinaryFileResponse
    {
        $label = ucfirst($type);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle($label);
        $headers = ['Number', 'Date AD', 'Date BS', 'Financial Year', 'Category', 'Account', 'Amount', 'Status', 'Reference'];

        foreach ($headers as $index => $header) {
            $sheet->setCellValueExplicit([$index + 1, 1], $header, DataType::TYPE_STRING);
        }

        foreach ($transactions as $rowIndex => $transaction) {
            $row = $rowIndex + 2;
            $number = $type === 'income' ? $transaction->income_number : $transaction->expense_number;
            $values = [
                $number,
                $transaction->date_ad?->format('Y-m-d') ?? '',
                $transaction->date_bs ?? '',
                $transaction->financial_year ?? '',
                $transaction->category?->name ?? '',
                $transaction->account?->name ?? '',
                $transaction->amount,
                ucfirst($transaction->status),
                $transaction->reference ?? '',
            ];

            foreach ($values as $columnIndex => $value) {
                if ($columnIndex === 6) {
                    $sheet->setCellValue([$columnIndex + 1, $row], $value);
                } else {
                    $sheet->setCellValueExplicit(
                        [$columnIndex + 1, $row],
                        (string) $value,
                        DataType::TYPE_STRING
                    );
                }
            }
        }

        $lastRow = $transactions->count() + 2;
        $sheet->setCellValue("F{$lastRow}", 'Total');
        $sheet->setCellValue("G{$lastRow}", $transactions->sum('amount'));
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle("F{$lastRow}:G{$lastRow}")->getFont()->setBold(true);
        $sheet->freezePane('A2');
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle("G2:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        $path = tempnam(sys_get_temp_dir(), $type.'-export-');
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return response()->download($path, $type.'-filtered-'.now()->format('Y-m-d').'.xlsx')
            ->deleteFileAfterSend(true);
    }
}
