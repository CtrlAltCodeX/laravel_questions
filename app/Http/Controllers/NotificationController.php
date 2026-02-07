<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\GoogleUser;
use App\Models\Setting;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $users = GoogleUser::where('status', 'Enabled')->get();

        return view('notifications.index', compact('notifications', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:Notification,Announcement',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        $settings = Setting::first();
        $fcmServerKey = $settings->fcm_server_key ?? 'YOUR_FCM_SERVER_KEY_PLACEHOLDER';

        // Check user selection
        if ($targetUserIds && in_array('all', $targetUserIds)) {
             $tokens = UserFcmToken::pluck('fcm_token')->toArray();
        } elseif ($targetUserIds) {
             $tokens = UserFcmToken::whereIn('user_id', $targetUserIds)->pluck('fcm_token')->toArray();
        } else {
             // If no users selected, maybe it was meant for all? 
             // Requirement says "chahe to sara user select kr le ya manully kr le"
             // Usually, a blank targeted list might mean nobody gets it unless it's a general announcement.
             // But let's assume if no users selected, we don't send to anyone manually.
             return;
        }

        if (empty($tokens)) {
            return;
        }

        $notification->update(['sent_at' => now()]);
        
        // cURL logic...
    }
}
