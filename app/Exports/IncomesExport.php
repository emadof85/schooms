<?php
// app/Exports/IncomesExport.php

namespace App\Exports;

class IncomesExport extends BaseFinanceExport
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
            'Received From',
            'Description'
        ];
    }

    public function map($income): array
    {
        return [
            $this->formatDate($income->income_date),
            $income->reference_no,
            $income->category->name ?? 'N/A',
            $income->title,
            $this->formatCurrency($income->amount),
            ucfirst($income->payment_method),
            $income->received_from,
            $income->description ?? 'N/A'
        ];
    }
}