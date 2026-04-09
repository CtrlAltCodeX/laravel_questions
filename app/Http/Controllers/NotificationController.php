<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\GoogleUser;
use App\Models\Setting;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function apiIndex(Request $request, $userId)
    {
        $perPage = $request->get('limit', 10);
        $notifications = Notification::where('user_id', $userId)
            ->orWhereNull('user_id')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
                'from' => $notifications->firstItem(),
                'to' => $notifications->lastItem(),
                'notifications' => $notifications->getCollection()->map(function ($n) {
            return [
                        'id' => $n->id,
                        'type' => strtolower($n->type),
                        'title' => $n->title,
                        'message' => $n->message,
                        'image_url' => $n->image ? asset('storage/' . $n->image) : null,
                        'is_read' => $n->is_read,
                        'created_at' => $n->created_at->toISOString(),
                        'action_url' => $n->action_url,
                        'source' => $n->source
                    ];
        })
            ]
        ]);
    }

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

        $data['source'] = 'admin';
        $data['user_id'] = null; // Admin usually sends to multiple or all

        // Logic to send notification using service
        $this->notificationService->send($data, $request->user_ids);

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

    public function markAsRead(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id'
        ]);

        Notification::whereIn('id', $request->notification_ids)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read successfully'
        ]);
    }

    public function deleteNotifications(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id'
        ]);

        Notification::whereIn('id', $request->notification_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifications deleted successfully'
        ]);
    }
}