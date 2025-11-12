<?php
// app/Exports/BaseFinanceExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseFinanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $records;
    protected $type;
    protected $startDate;
    protected $endDate;
    protected $category;

    public function __construct($records, $type, $startDate, $endDate, $category = null)
    {
        $this->records = $records;
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->category = $category;
    }

    public function collection()
    {
        return $this->records;
    }

    abstract public function headings(): array;
    
    abstract public function map($record): array;

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:G1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'E8E8E8']
                ]
            ],
        ];
    }

    protected function formatCurrency($amount)
    {
        return number_format($amount, 2);
    }

    protected function formatDate($dateString)
    {
        return date('M d, Y', strtotime($dateString));
    }
}