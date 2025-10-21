<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Payment::with(['user', 'course'])
            ->get()
            ->map(function ($payment) {
                return [
                    'Payment Id' => $payment->payment_id ,
                    'User Name' => $payment->user->name ?? '-',
                    'Course Name' => $payment->course->name ?? '-',
                    'Amount' => $payment->amount,
                    'Currency' => $payment->currency,
                    'Status' => ucfirst($payment->status),
                    'Email' => $payment->email,
                    'Contact' => $payment->contact,
                    'Date' => $payment->created_at->format('d/m/Y h:i A'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Payment Id',
            'User Name',
            'Course Name',
            'Amount',
            'Currency',
            'Status',
            'Email',
            'Contact',
            'Date/Time',
        ];
    }
}
