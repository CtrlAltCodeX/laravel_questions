<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\GoogleUser;
use App\Models\Setting;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Notification::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('message', 'like', '%' . $request->search . '%');
        }

        $notifications = $query->orderBy('id', 'desc')->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:Notification,Announcement',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_title' => 'nullable|string|max:255',
            'link_url' => 'nullable|url',
        ]);

        $data = $request->except(['image', 'user_ids']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/notifications', $filename);
            $data['image'] = 'notifications/' . $filename;
        }

        $notification = Notification::create($data);

        // Logic to send FCM
        $this->sendNotification($notification, $request->user_ids);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification created and sent successfully.']);
        }

        return redirect()->route('notifications.index')->with('success', 'Notification created and sent successfully.');
    }

    public function edit($id)
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        return response()->json(['notification' => $notification]);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:Notification,Announcement',
            'link_title' => 'nullable|string|max:255',
            'link_url' => 'nullable|url',
        ]);

        $data = $request->except(['image', 'user_ids']);

        if ($request->hasFile('image')) {
            if ($notification->image) {
                Storage::delete('public/' . $notification->image);
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/notifications', $filename);
            $data['image'] = 'notifications/' . $filename;
        }

        $notification->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Notification updated successfully.']);
        }

        return redirect()->route('notifications.index')->with('success', 'Notification updated successfully.');
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        if ($notification->image) {
            Storage::delete('public/' . $notification->image);
        }
        $notification->delete();

        return redirect()->route('notifications.index')->with('success', 'Notification deleted successfully.');
    }

    private function sendNotification($notification, $targetUserIds = null)
    {
        // Path to static JSON file
        $credentialsFilePath = storage_path('app/firebase-credentials.json');

        if (!file_exists($credentialsFilePath)) {
            \Log::error('Firebase credentials file not found at ' . $credentialsFilePath);
            return;
        }

        // Initialize Google Client
        $client = new \Google\Client();
        try {
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();

            $token = $client->getAccessToken();
        }
        catch (\Exception $e) {
            \Log::error('Firebase Google Client Error: ' . $e->getMessage());
            return;
        }

        if (!isset($token['access_token'])) {
            \Log::error('Failed to get FCM access token');
            return;
        }

        $accessToken = $token['access_token'];

        // Get Project ID from JSON
        $credentials = json_decode(file_get_contents($credentialsFilePath), true);
        $projectId = $credentials['project_id'] ?? null;

        if (!$projectId) {
            \Log::error('Project ID not found in firebase credentials file.');
            return;
        }

        $apiUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Do not send if scheduled for future
        if ($notification->schedule_at && $notification->schedule_at > now()) {
            return;
        }

        // Check user selection
        if ($targetUserIds && in_array('all', $targetUserIds)) {
            $tokens = UserFcmToken::pluck('fcm_token')->toArray();
        }
        elseif ($targetUserIds) {
            $tokens = UserFcmToken::whereIn('user_id', $targetUserIds)->pluck('fcm_token')->toArray();
        }
        else {
            return;
        }

        if (empty($tokens)) {
            return;
        }

        $success = false;

        // FCM HTTP v1 requires sending individual requests per token.
        // For larger lists, we should ideally use async HTTP pooling, 
        // but here we use a synchronous loop as a starting point.
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
                        'link_title' => (string)$notification->link_title,
                        'link_url' => (string)$notification->link_url,
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
                    \Log::error('FCM Send Error for token ' . $deviceToken . ': ' . $response->body());
                }
            }
            catch (\Exception $e) {
                \Log::error('FCM Send Exception: ' . $e->getMessage());
            }
        }

        if ($success) {
            $notification->update(['sent_at' => now()]);
        }
    }

    public function searchUsers(Request $request)
    {
        $search = $request->get('q');
        $users = GoogleUser::where('status', 'Enabled')
            ->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                }
                );
            })
            ->select('id', 'name', 'email')
            ->limit(20)
            ->get();

        return response()->json($users);
    }
}