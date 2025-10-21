<?php

namespace App\Exports;

use App\Models\GoogleUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class UserAnalyticsExport implements FromCollection, WithHeadings
{
    protected $range;

    public function __construct($range = '7days')
    {
        $this->range = $range;
    }

    public function collection()
    {
        // Determine start date
        $startDate = null;
        if ($this->range === '7days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
        } elseif ($this->range === '1month') {
            $startDate = Carbon::now()->subMonth()->startOfDay();
        }

        // Get grouped data
        $query = GoogleUser::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->pluck('total', 'date');

        // Build full date list
        $dates = collect();
        $minDate = GoogleUser::min('created_at');
        if (!$minDate) {
            return collect(); // no users
        }

        $current = Carbon::parse($startDate ?? $minDate)->startOfDay();
        $end = Carbon::now()->startOfDay();

        while ($current <= $end) {
            $dates->push([
                'date' => $current->toDateString(),
                'total' => $query[$current->toDateString()] ?? 0
            ]);
            $current->addDay();
        }

        // If "all" range, remove zero totals (just like AJAX)
        if ($this->range === 'all') {
            $dates = $dates->filter(fn($item) => $item['total'] > 0)->values();
        }

        return $dates;
    }

    public function headings(): array
    {
        return ['Date', 'Total Users'];
    }
}
