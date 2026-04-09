<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to specific users or all users.
     *
     * @param array $data Notification data (title, message, user_id, type, source, image, etc.)
     * @param array|null $targetUserIds Array of user IDs or ['all']
     * @return Notification
     */
    public function send($data, $targetUserIds = null)
    {
        // 1. Create Notification Record
        $notification = Notification::create([
            'user_id' => $data['user_id'] ?? null,
            'title' => $data['title'],
            'message' => $data['message'],
            'image' => $data['image'] ?? null,
            'type' => $data['type'] ?? 'Notification',
            'source' => $data['source'] ?? 'admin',
            'link_title' => $data['link_title'] ?? null,
            'link_url' => $data['link_url'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'schedule_at' => $data['schedule_at'] ?? null,
        ]);

        // 2. Dispatch FCM if not scheduled for future
        if (!$notification->schedule_at || $notification->schedule_at <= now()) {
            $this->dispatchFCM($notification, $targetUserIds);
        }

        return $notification;
    }

    /**
     * Dispatch FCM Push Notification
     */
    protected function dispatchFCM(Notification $notification, $targetUserIds = null)
    {
        $credentialsFilePath = config('services.firebase.credentials');

        if (!file_exists($credentialsFilePath)) {
            Log::error('Firebase credentials file not found at ' . $credentialsFilePath);
            return;
        }

        // Get tokens
        if ($targetUserIds && in_array('all', $targetUserIds)) {
            $tokens = UserFcmToken::pluck('fcm_token')->toArray();
        }
        elseif ($targetUserIds) {
            $tokens = UserFcmToken::whereIn('user_id', $targetUserIds)->pluck('fcm_token')->toArray();
        }
        elseif ($notification->user_id) {
            $tokens = UserFcmToken::where('user_id', $notification->user_id)->pluck('fcm_token')->toArray();
        }
        else {
            return;
        }

        if (empty($tokens)) {
            return;
        }

        $accessToken = $this->getFcmAccessToken($credentialsFilePath);
        if (!$accessToken)
            return;

        $credentials = json_decode(file_get_contents($credentialsFilePath), true);
        $projectId = $credentials['project_id'] ?? null;
        if (!$projectId)
            return;

        $apiUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $success = false;

        foreach (array_unique($tokens) as $deviceToken) {
            $messagePayload = [
                'message' => [
                    'token' => $deviceToken,
                    'notification' => [
                        'title' => $notification->title,
                        'body' => $notification->message,
                    ],
                    'data' => [
                        'type' => (string)$notification->type,
                        'source' => (string)$notification->source,
                        'action_url' => (string)$notification->action_url,
                        'notification_id' => (string)$notification->id
                    ]
                ]
            ];

            if ($notification->image) {
                $imageUrl = asset('storage/' . $notification->image);
                $messagePayload['message']['notification']['image'] = $imageUrl;
                $messagePayload['message']['data']['image'] = $imageUrl;
            }

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])->post($apiUrl, $messagePayload);

                if ($response->successful()) {
                    $success = true;
                }
                else {
                    Log::error('FCM Send Error for token ' . $deviceToken . ': ' . $response->body());
                }
            }
            catch (\Exception $e) {
                Log::error('FCM Send Exception: ' . $e->getMessage());
            }
        }

        if ($success) {
            $notification->update(['sent_at' => now()]);
        }
    }

    /**
     * Get OAuth2 Access Token for FCM
     */
    protected function getFcmAccessToken($credentialsFilePath)
    {
        $client = new \Google\Client();
        try {
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();
            $token = $client->getAccessToken();
            return $token['access_token'] ?? null;
        }
        catch (\Exception $e) {
            Log::error('Firebase Google Client Error: ' . $e->getMessage());
            return null;
        }
    }
}