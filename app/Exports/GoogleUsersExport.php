<?php

namespace App\Exports;

use App\Models\GoogleUser;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GoogleUsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return GoogleUser::with(['category.language', 'userCourses.course'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Language',
            'Category',
            'Coins',
            'Login Type',
            'Status',
            'Created At'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone_number,
            $user->category->language->name ?? '-',
            $user->category->name ?? '-',
            $user->coins,
            $user->login_type,
            $user->status,
            $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
