<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    /**
     * الحصول على أو إنشاء محادثة المشروع
     */
    public function getOrCreateProjectConversation(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // التأكد من أن المستخدم لديه صلاحية الوصول للمشروع
        Gate::authorize('view', $project);
        
        // التحقق من حالة المشروع
        if ($project->status === 'مكتمل') {
            return response()->json([
                'success' => false,
                'message' => 'المشروع مكتمل ولا يمكن إرسال رسائل',
            ], 403);
        }
        
        // البحث عن محادثة موجودة أو إنشاء واحدة جديدة
        $conversation = Conversation::firstOrCreate(
            [
                'type' => 'project',
                'project_id' => $projectId,
            ],
            [
                'title' => $project->name,
                'is_closed' => false,
            ]
        );
        
        // إعادة فتح الشات إذا كان مغلقاً والمشروع لم ينته
        if ($conversation->is_closed && $project->status !== 'مكتمل') {
            $conversation->open();
        }
        
        // إغلاق الشات إذا المشروع مكتمل
        if ($project->status === 'مكتمل' && !$conversation->is_closed) {
            $conversation->close();
        }
        
        $conversation->load(['messages.user', 'project']);
        
        // تحديد الرسائل كمقروءة للمستخدم الحالي فقط عند فتح الشات للمرة الأولى
        $conversation->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        return response()->json([
            'success' => true,
            'conversation' => $conversation->fresh(),
            'messages' => $conversation->messages()->with('user')->latest()->paginate(50),
            'participants' => $conversation->participants,
        ]);
    }
    
    /**
     * الحصول على أو إنشاء محادثة فردية
     */
    public function getOrCreatePrivateConversation(Request $request, $userId)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userId);
        
        // البحث عن محادثة موجودة بين المستخدمين
        $conversation = Conversation::where('type', 'private')
            ->where(function($query) use ($currentUser, $otherUser) {
                $query->where(function($q) use ($currentUser, $otherUser) {
                    $q->where('user1_id', $currentUser->id)
                      ->where('user2_id', $otherUser->id);
                })->orWhere(function($q) use ($currentUser, $otherUser) {
                    $q->where('user1_id', $otherUser->id)
                      ->where('user2_id', $currentUser->id);
                });
            })
            ->first();
        
        // إنشاء محادثة جديدة إذا لم توجد
        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'private',
                'user1_id' => $currentUser->id,
                'user2_id' => $otherUser->id,
                'title' => "محادثة مع {$otherUser->name}",
                'is_closed' => false,
            ]);
        }
        
        $conversation->load(['messages.user', 'user1', 'user2']);
        
        return response()->json([
            'success' => true,
            'conversation' => $conversation,
            'messages' => $conversation->messages()->with('user')->latest()->paginate(50),
        ]);
    }
    
    /**
     * إرسال رسالة
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
        ]);
        
        // يجب أن يكون هناك رسالة أو مرفق على الأقل
        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json([
                'success' => false,
                'message' => 'يجب إدخال رسالة أو إرفاق ملف',
            ], 422);
        }
        
        $conversation = Conversation::findOrFail($conversationId);
        
        // التحقق من إمكانية إرسال الرسالة
        if (!$conversation->canSendMessage()) {
            return response()->json([
                'success' => false,
                'message' => 'المحادثة مغلقة ولا يمكن إرسال رسائل',
            ], 403);
        }
        
        // التحقق من صلاحية المستخدم
        if ($conversation->type === 'project') {
            Gate::authorize('view', $conversation->project);
        } else {
            // للشات الفردي، يجب أن يكون المستخدم أحد المشاركين
            if ($conversation->user1_id !== Auth::id() && $conversation->user2_id !== Auth::id()) {
                abort(403, 'غير مصرح لك بإرسال رسائل في هذه المحادثة');
            }
        }
        
        DB::beginTransaction();
        try {
            // رفع المرفق إن وجد
            $attachmentPath = null;
            $attachmentName = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('chat-attachments', 'public');
                $attachmentName = $request->file('attachment')->getClientOriginalName();
            }
            
            // إنشاء الرسالة
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'user_id' => Auth::id(),
                'message' => $request->message,
                'attachment' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'is_read' => false,
            ]);
            
            // تحديث آخر نشاط للمحادثة
            $conversation->update([
                'last_message_at' => now(),
            ]);
            
            $message->load('user');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'notification' => 'تم إرسال الرسالة بنجاح',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * الحصول على الرسائل
     */
    public function getMessages(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        // التحقق من الصلاحية
        if ($conversation->type === 'project') {
            Gate::authorize('view', $conversation->project);
        } else {
            if ($conversation->user1_id !== Auth::id() && $conversation->user2_id !== Auth::id()) {
                abort(403, 'غير مصرح لك بالوصول لهذه المحادثة');
            }
        }
        
        $lastMessageId = $request->get('last_message_id');
        
        $query = $conversation->messages()->with('user')->latest();
        
        // إذا كان هناك last_message_id، اجلب فقط الرسائل الجديدة
        if ($lastMessageId) {
            $query->where('id', '>', $lastMessageId);
        }
        
        $messages = $query->paginate(50);
        
        // تحديد الرسائل كمقروءة للمستخدم الحالي فقط عند عدم وجود last_message_id
        // (يعني أن المستخدم فتح الشات للمرة الأولى وليس polling)
        if (!$lastMessageId) {
            $conversation->messages()
                ->where('user_id', '!=', Auth::id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }
        
        return response()->json([
            'success' => true,
            'messages' => $messages,
            'conversation' => $conversation->fresh(),
        ]);
    }
    
    /**
     * الحصول على المحادثات الفردية للمستخدم
     */
    public function getPrivateConversations(Request $request)
    {
        $user = Auth::user();
        
        $conversations = Conversation::where('type', 'private')
            ->where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->with(['user1', 'user2', 'lastMessage.user'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function($conversation) use ($user) {
                $otherUser = $conversation->user1_id === $user->id 
                    ? $conversation->user2 
                    : $conversation->user1;
                
                return [
                    'id' => $conversation->id,
                    'other_user' => $otherUser,
                    'last_message' => $conversation->lastMessage,
                    'unread_count' => $conversation->messages()
                        ->where('user_id', '!=', $user->id)
                        ->where('is_read', false)
                        ->count(),
                    'updated_at' => $conversation->updated_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }
    
    /**
     * حذف رسالة
     */
    public function deleteMessage(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // فقط المرسل يمكنه حذف الرسالة
        if ($message->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذه الرسالة');
        }
        
        // حذف المرفق إن وجد
        if ($message->attachment && Storage::disk('public')->exists($message->attachment)) {
            Storage::disk('public')->delete($message->attachment);
        }
        
        $message->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف الرسالة بنجاح',
        ]);
    }
    
    /**
     * الحصول على عدد الرسائل غير المقروءة للمستخدم في المشاريع المشترك فيها
     */
    public function getUnreadMessagesCount(Request $request)
    {
        $user = Auth::user();
        
        // الحصول على جميع المشاريع التي المستخدم مشترك فيها
        // استخدام نفس الطريقة المستخدمة في ProjectsController و DashboardController
        $userProjectsQuery = Project::query();
        
        // Super Admin يرى الكل (بما في ذلك المشاريع المخفية)
        if (!$user->hasRole('super_admin')) {
            // للمستخدمين العاديين: إخفاء المشاريع المخفية
            $userProjectsQuery->where('is_hidden', false);
            $userProjectsQuery->where(function($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                  ->orWhereHas('teamUsers', function($query) use ($user) {
                      $query->where('users.id', $user->id);
                  })
                  ->orWhereJsonContains('team_members', (string)$user->id)
                  ->orWhereJsonContains('team_members', $user->id);
            });
        }
        // Super Admin لا يحتاج فلاتر - يرى الكل
        
        $userProjects = $userProjectsQuery->pluck('id');
        
        if ($userProjects->isEmpty()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'projects_with_messages' => [],
            ]);
        }
        
        // الحصول على جميع المحادثات للمشاريع المشترك فيها
        $conversations = Conversation::where('type', 'project')
            ->whereIn('project_id', $userProjects)
            ->where('is_closed', false)
            ->get();
        
        $unreadCount = 0;
        $projectsWithMessages = [];
        
        foreach ($conversations as $conversation) {
            // حساب الرسائل غير المقروءة في هذه المحادثة
            $messagesUnread = $conversation->messages()
                ->where('user_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();
            
            if ($messagesUnread > 0) {
                $unreadCount += $messagesUnread;
                
                // الحصول على آخر رسالة غير مقروءة
                $lastUnreadMessage = $conversation->messages()
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->with('user')
                    ->latest()
                    ->first();
                
                $projectsWithMessages[] = [
                    'project_id' => $conversation->project_id,
                    'project_name' => $conversation->project->name ?? $conversation->title,
                    'conversation_id' => $conversation->id,
                    'unread_count' => $messagesUnread,
                    'last_message' => $lastUnreadMessage ? [
                        'id' => $lastUnreadMessage->id,
                        'message' => $lastUnreadMessage->message,
                        'user' => [
                            'id' => $lastUnreadMessage->user->id,
                            'name' => $lastUnreadMessage->user->name,
                            'avatar_url' => $lastUnreadMessage->user->avatar_url ?? null,
                        ],
                        'created_at' => $lastUnreadMessage->created_at->toISOString(),
                    ] : null,
                ];
            }
        }
        
        // ترتيب المشاريع حسب آخر رسالة غير مقروءة
        usort($projectsWithMessages, function($a, $b) {
            if ($a['last_message'] && $b['last_message']) {
                return strtotime($b['last_message']['created_at']) - strtotime($a['last_message']['created_at']);
            }
            return 0;
        });
        
        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'projects_with_messages' => $projectsWithMessages,
        ]);
    }
}
