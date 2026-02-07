<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GoogleUser;
use App\Models\UserFcmToken;
use Illuminate\Support\Str;

class PopulateGoogleUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-users:populate';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Populate google_users table with dummy data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = [
            ['name' => 'Mahesh', 'email' => 'mahesh.tech@gmail.com'],
            ['name' => 'Pinku Kumar', 'email' => 'pinku15%@gmail.com'],
            ['name' => 'Chandbi', 'email' => 'chandbi.india@gmail.com'],
            ['name' => 'Ram Mohan', 'email' => 'ram.m.72@gmail.com'],
            ['name' => 'Debabrata', 'email' => 'debabrata.biswal26@gmail.com'],
            ['name' => 'Himanshu', 'email' => 'himansha16@gmail.com'],
            ['name' => 'Rammohan Choudhary', 'email' => 'ram8605@gmail.com'],
        ];

        foreach ($users as $userData) {
            $user = GoogleUser::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'status' => 'Enabled',
                ]
            );

            UserFcmToken::updateOrCreate(
                ['user_id' => $user->id],
                ['fcm_token' => 'dummy_token_' . Str::random(10)]
            );
        }

        $this->info('Successfully populated google_users with dummy data and tokens.');
    }
}
