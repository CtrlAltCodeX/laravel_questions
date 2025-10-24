<?php

namespace App\Exports;

use App\Models\Payment;
use App\Models\UserCoin;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class PaymentsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
    
        $payments = Payment::with(['user', 'course'])->get();

        $userCoins = UserCoin::with('user')->get();


        $mergedData = $payments->map(function ($item) {
            return [
                'Source' => 'Payment',
                'User Name' => $item->user->name ?? '-',
                'Email' => $item->email ?? '-',
                'Contact' => $item->contact ?? '-',
                'Course Name' => $item->course->name ?? '-',
                'Amount' => $item->amount ?? '-',
                'Currency' => $item->currency ?? '-',
                'Payment Id' => $item->payment_id ?? '-',
                'Method' => $item->method ?? '-',
                'Card Network' => $item->card_network ?? '-',
                'Card Last4' => $item->card_last4 ?? '-',
                'VPA' => $item->vpa ?? '-',
                'Status' => ucfirst($item->status ?? '-'),
                'Date / Time' => optional($item->created_at)->format('d/m/Y h:i A'),
            ];
        });

        $userCoinData = $userCoins->map(function ($item) {
            return [
                'Source' => 'User Coin',
                'User Name' => $item->user->name ?? '-',
                'Email' => $item->user->email ?? '-',
                'Contact' => $item->user->phone_number ?? '-',
                'Course Name' => '-',
                'Amount' => $item->coin ?? '-',
                'Currency' => 'INR',
                'Payment Id' => $item->meta_description ?? '-',
                'Method' => '-',
                'Card Network' => '-',
                'Card Last4' => '-',
                'VPA' => '-',
                'Status' => $item->user->status ?? '-',
                'Date / Time' => optional($item->created_at)->format('d/m/Y h:i A'),
            ];
        });

        $merged = $mergedData->merge($userCoinData)->sortByDesc('Date / Time');

        return new Collection($merged->values());
    }

    public function headings(): array
    {
        return [
            'Source',
            'User Name',
            'Email',
            'Contact',
            'Course Name',
            'Amount',
            'Currency',
            'Payment Id',
            'Method',
            'Card Network',
            'Card Last4',
            'VPA',
            'Status',
            'Date / Time',
        ];
    }
}
