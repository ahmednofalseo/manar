<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $locale = app()->getLocale();
        
        $query = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');
        
        // Get unread count
        $unreadCount = (clone $query)->where('read', false)->count();
        
        // Get notifications (limit to 50)
        $notifications = $query->limit(50)->get()->map(function ($notification) use ($locale) {
            // Translate title and message based on current locale
            $title = $this->translateNotificationTitle($notification->type, $locale);
            $message = $this->translateNotificationMessage($notification, $locale);
            
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $title,
                'message' => $message,
                'data' => $notification->data,
                'read' => (bool) $notification->read,
                'created_at' => $notification->created_at->toISOString(),
            ];
        });
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'notifications' => $notifications->values(),
                'unread_count' => $unreadCount,
            ]);
        }
        
        return view('notifications.index', compact('notifications', 'unreadCount'));
    }
    
    /**
     * Translate notification title
     */
    private function translateNotificationTitle(string $type, string $locale): string
    {
        if ($locale === 'en') {
            return $type === 'task_assigned' 
                ? 'New Task Assigned to You'
                : 'New Comment on Task';
        }
        
        return $type === 'task_assigned'
            ? 'مهمة جديدة مخصصة لك'
            : 'تعليق جديد على المهمة';
    }
    
    /**
     * Translate notification message
     */
    private function translateNotificationMessage(Notification $notification, string $locale): string
    {
        $data = $notification->data ?? [];
        $taskTitle = $data['task_title'] ?? '';
        $projectName = $data['project_name'] ?? null;
        
        if ($locale === 'en') {
            if ($notification->type === 'task_assigned') {
                $message = "A new task has been assigned to you: {$taskTitle}";
                if ($projectName) {
                    $message .= " in project: {$projectName}";
                }
                return $message;
            } else {
                return "A new comment has been added to your task: {$taskTitle}";
            }
        }
        
        // Arabic
        if ($notification->type === 'task_assigned') {
            $message = "تم تعيين مهمة جديدة لك: {$taskTitle}";
            if ($projectName) {
                $message .= " في مشروع: {$projectName}";
            }
            return $message;
        } else {
            return "تم إضافة تعليق جديد على مهمتك: {$taskTitle}";
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->markAsRead();
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Notification marked as read'),
            ]);
        }
        
        return back()->with('success', __('Notification marked as read'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('All notifications marked as read'),
            ]);
        }
        
        return back()->with('success', __('All notifications marked as read'));
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->delete();
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Notification deleted'),
            ]);
        }
        
        return back()->with('success', __('Notification deleted'));
    }

    /**
     * Delete all notifications
     */
    public function destroyAll(Request $request)
    {
        Notification::where('user_id', Auth::id())->delete();
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('All notifications deleted'),
            ]);
        }
        
        return back()->with('success', __('All notifications deleted'));
    }
}
