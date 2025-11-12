<?php
// app/Exports/ExpensesExport.php

namespace App\Exports;

class ExpensesExport extends BaseFinanceExport
{
    public function headings(): array
    {
        return [
            'Date',
            'Reference No',
            'Category',
            'Title',
            'Amount',
            'Payment Method',
            'Paid To',
            'Description'
        ];
    }

    public function map($expense): array
    {
        return [
            $this->formatDate($expense->expense_date),
            $expense->reference_no,
            $expense->category->name ?? 'N/A',
            $expense->title,
            $this->formatCurrency($expense->amount),
            ucfirst($expense->payment_method),
            $expense->paid_to,
            $expense->description ?? 'N/A'
        ];
    }
}