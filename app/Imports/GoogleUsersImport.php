<?php

namespace App\Imports;

use App\Models\GoogleUser;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GoogleUsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return GoogleUser::updateOrCreate(
            ['email' => $row['email']],
            [
                'name'         => $row['name'],
                'phone_number' => $row['phone'] ?? $row['phone_number'] ?? null,
                'status'       => $row['status'] ?? 'Enabled',
            ]
        );
    }
}
