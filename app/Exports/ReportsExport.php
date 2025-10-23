<?php

namespace App\Exports;

use App\Models\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportsExport implements FromCollection, WithHeadings
{
    // Collection of data
    public function collection()
    {
        // Agar aap pagination ignore karke saari records export karna chahte ho
        return Report::all()->map(function($report) {
            return [
                'Id'      => $report->id,
                'Name'    => $report->name,
                'Title'   => $report->title,
                'Type'    => $report->type,
                'Message' => $report->message,
                'Date'    => $report->date,
            ];
        });
    }

    // Column headings
    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Title',
            'Type',
            'Message',
            'Date',
        ];
    }
}
