<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskAttachment;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Events\TaskCreated;
use App\Events\TaskCommented;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TasksController extends Controller
{

    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Task::with(['project', 'projectStage', 'assignee'])->whereNotNull('project_id');

        // تطبيق فلاتر الصلاحيات - المستخدم يرى المهام المخصصة له فقط
        if (!$user->hasRole('super_admin')) {
            // للمستخدمين العاديين: يروا المهام المسندة إليهم أو المهام في المشاريع التي هم مديرين عليها أو أعضاء في فريقها
            $projectIds = Project::where('is_hidden', false) // إخفاء المشاريع المخفية
                ->where(function($q) use ($user) {
                    $q->where('project_manager_id', $user->id)
                      ->orWhereJsonContains('team_members', (string)$user->id)
                      ->orWhereJsonContains('team_members', $user->id);
                })->pluck('id');
            
            $query->where(function($q) use ($user, $projectIds) {
                $q->where('assignee_id', $user->id) // المهام المسندة مباشرة للمستخدم
                  ->orWhereIn('project_id', $projectIds); // المهام في مشاريعه
            });
        }
        // Super Admin يرى الكل (لا فلاتر)

        // الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('project_stage_id')) {
            // البحث بالاسم (stage_name)
            $query->whereHas('projectStage', function($q) use ($request) {
                $q->where('stage_name', $request->project_stage_id);
            });
        }

        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->assignee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // KPIs - استخدام نفس قاعدة الاستعلام مع فلاتر الصلاحيات
        $baseQuery = Task::query()->whereNotNull('project_id');
        
        // تطبيق نفس فلاتر الصلاحيات على KPIs
        if (!$user->hasRole('super_admin')) {
            $projectIds = Project::where('is_hidden', false) // إخفاء المشاريع المخفية
                ->where(function($q) use ($user) {
                    $q->where('project_manager_id', $user->id)
                      ->orWhereJsonContains('team_members', (string)$user->id)
                      ->orWhereJsonContains('team_members', $user->id);
                })->pluck('id');
            
            $baseQuery->where(function($q) use ($user, $projectIds) {
                $q->where('assignee_id', $user->id)
                  ->orWhereIn('project_id', $projectIds);
            });
        }
        
        $totalTasks = (clone $baseQuery)->count();
        $inProgressTasks = (clone $baseQuery)->where('status', 'in_progress')->count();
        $completedTasks = (clone $baseQuery)->where('status', 'done')->count();
        $overdueTasks = (clone $baseQuery)->where('due_date', '<', now())
            ->whereNotIn('status', ['done', 'rejected'])->count();

        $tasks = $query->latest()->paginate(20);

        // البيانات للـ dropdowns (تحسين - cache إذا أمكن)
        $projects = Project::select('id', 'name', 'project_number')
            ->with('projectStages:id,project_id,stage_name')
            ->get();
        
        $engineers = User::select('id', 'name')
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['engineer', 'project_manager']);
            })
            ->orderBy('name')
            ->get();
        
        // الحصول على جميع المراحل المميزة (تحسين - select فقط الحقول المطلوبة)
        $stages = \App\Models\ProjectStage::select('stage_name')
            ->distinct()
            ->orderBy('stage_name')
            ->pluck('stage_name');

        // ========== الإحصائيات الجديدة ==========
        
        // 1. نسبة إنجاز المشاريع (متوسط نسبة التقدم)
        $projectsQuery = Project::query()->where('is_hidden', false); // إخفاء المشاريع المخفية
        if ($user->hasRole('project_manager')) {
            $projectsQuery->where('project_manager_id', $user->id)
                ->orWhereHas('teamUsers', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
        }
        $totalProjects = $projectsQuery->count();
        $averageProjectProgress = $totalProjects > 0 
            ? round($projectsQuery->avg('progress') ?? 0) 
            : 0;

        // 2. أفضل 5 مهندسين (حسب عدد المهام المنجزة ونسبة الإنجاز)
        $topEngineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['engineer', 'project_manager']);
        })
        ->withCount([
            'assignedTasks as completed_tasks_count' => function($query) {
                $query->where('status', 'done');
            },
            'assignedTasks as total_tasks_count'
        ])
        ->whereHas('assignedTasks')
        ->get()
        ->map(function($engineer) {
            $totalTasks = $engineer->total_tasks_count ?? 0;
            $completedTasks = $engineer->completed_tasks_count ?? 0;
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            
            return [
                'id' => $engineer->id,
                'name' => $engineer->name,
                'job_title' => $engineer->job_title,
                'avatar' => $engineer->avatar,
                'completed_tasks' => $completedTasks,
                'total_tasks' => $totalTasks,
                'completion_rate' => $completionRate,
            ];
        })
        ->sortByDesc(function($engineer) {
            // ترتيب حسب نسبة الإنجاز أولاً، ثم عدد المهام المنجزة
            return [$engineer['completion_rate'], $engineer['completed_tasks']];
        })
        ->take(5)
        ->values();

        // 3. عدد المهام حسب المرحلة
        $tasksByStatus = [
            'new' => (clone $baseQuery)->where('status', 'new')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'done' => (clone $baseQuery)->where('status', 'done')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        // بيانات إضافية للـ Charts
        // 1. بيانات المشاريع (مكتمل، قيد التنفيذ، متأخر)
        $completedProjects = (clone $projectsQuery)->where('status', 'مكتمل')->count();
        $inProgressProjects = (clone $projectsQuery)->where('status', 'قيد التنفيذ')->count();
        $delayedProjects = (clone $projectsQuery)->where('status', 'متوقف')->count();
        
        // 2. بيانات أفضل المهندسين للـ Chart
        $topEngineersForChart = $topEngineers->map(function($engineer) {
            return [
                'name' => $engineer['name'],
                'completed_tasks' => $engineer['completed_tasks']
            ];
        });

        return view('tasks.index', compact(
            'tasks', 
            'projects', 
            'engineers', 
            'stages', 
            'totalTasks', 
            'inProgressTasks', 
            'completedTasks', 
            'overdueTasks',
            'averageProjectProgress',
            'topEngineers',
            'tasksByStatus',
            'completedProjects',
            'inProgressProjects',
            'delayedProjects',
            'topEngineersForChart'
        ));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Task::class);

        $projects = Project::with('projectStages')->get();
        $engineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['engineer', 'project_manager']);
        })->get();

        $selectedProjectId = $request->query('project_id');

        return view('tasks.create', compact('projects', 'engineers', 'selectedProjectId'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        DB::beginTransaction();
        try {
            $task = Task::create([
                'project_id' => $request->project_id,
                'project_stage_id' => $request->project_stage_id,
                'assignee_id' => $request->assignee_id,
                'created_by' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'manager_notes' => $request->manager_notes,
                'priority' => $request->priority ?? 'medium',
                'start_date' => $request->start_date,
                'due_date' => $request->due_date,
                'progress' => $request->progress ?? 0,
                'status' => 'new',
            ]);

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filePath = $file->store('task-attachments', 'public');
                    
                    TaskAttachment::create([
                        'task_id' => $task->id,
                        'name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                    ]);

                    // تسجيل رفع المرفق في سجل النشاط
                    $task->logActivity('attachment', Auth::id(), null, null, "تم رفع الملف: {$file->getClientOriginalName()}");
                }
            }

            // تسجيل الإجراء
            $task->logActivity('assignment', Auth::id(), null, $task->status, "إنشاء مهمة جديدة: {$task->title}");

            // إرسال إشعار للموظف المخصص
            TaskCreated::dispatch($task);

            DB::commit();

            return redirect()->route('tasks.show', $task->id)
                ->with('success', 'تم إنشاء المهمة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المهمة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified task.
     */
    public function show(string $id)
    {
        $task = Task::with([
            'project.teamUsers',
            'projectStage.tasks.assignee',
            'projectStage', 
            'assignee', 
            'creator', 
            'notes.user',
            'attachments.uploader'
        ])->findOrFail($id);

        Gate::authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        Gate::authorize('update', $task);

        $projects = Project::with('projectStages')->orderBy('name')->get();
        $engineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['engineer', 'project_manager']);
        })->orderBy('name')->get();
        
        // تحميل project stages للمشروع الحالي
        $task->load('project.projectStages');

        return view('tasks.edit', compact('task', 'projects', 'engineers'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        $task = Task::findOrFail($id);
        // Authorization is already checked in UpdateTaskRequest

        $user = Auth::user();
        $isEmployee = $user->hasRole('engineer') && $task->assignee_id === $user->id;

        DB::beginTransaction();
        try {
            $oldStatus = $task->status;
            
            // الموظف يمكنه تحديث حقول محدودة فقط
            if ($isEmployee) {
                $task->update($request->only(['description', 'progress', 'completion_notes']));
            } else {
                // Managers و Admins يمكنهم تحديث كل شيء
                $task->update($request->validated());
            }

            // إعادة تحميل المهمة مع العلاقات بعد التحديث
            $task->refresh();
            $task->load(['project.teamUsers', 'projectStage', 'assignee']);

            // تسجيل التغييرات
            $statusChanged = $task->status !== $oldStatus;
            if ($statusChanged) {
                $task->logActivity('status_change', Auth::id(), $oldStatus, $task->status);
            }

            // لا نضيف تعليق تلقائي عند التحديث لتجنب التكرار
            // التعليقات يجب أن تكون فقط من المستخدمين

            DB::commit();

            return redirect()->route('tasks.show', $task->id)
                ->with('success', 'تم تحديث المهمة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث المهمة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'تم حذف المهمة بنجاح');
    }

    /**
     * Change task status.
     */
    public function changeStatus(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        Gate::authorize('changeStatus', $task);

        $validated = $request->validate([
            'status' => 'required|in:new,in_progress,done,rejected',
            'reason' => 'required_if:status,rejected|string|nullable',
            'completion_notes' => 'nullable|string',
        ], [
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة غير صحيحة',
            'reason.required_if' => 'سبب الرفض مطلوب عند رفض المهمة',
        ]);

        // التحقق من إمكانية الانتقال
        if (!$task->canChangeStatus($validated['status'])) {
            $errorMsg = 'لا يمكن الانتقال من ' . $task->status . ' إلى ' . $validated['status'];
            if ($request->expectsJson() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $task->status;
            $task->status = $validated['status'];

            if ($validated['status'] === 'rejected') {
                $task->rejection_reason = $validated['reason'] ?? null;
                $task->rejected_by = Auth::id();
                $task->rejected_at = now();
                $task->logActivity('rejection', Auth::id(), $oldStatus, 'rejected', null, $validated['reason'] ?? null);
            } elseif ($validated['status'] === 'done') {
                $task->completed_at = now();
                $task->progress = 100;
                if (isset($validated['completion_notes'])) {
                    $task->completion_notes = $validated['completion_notes'];
                }
                $task->logActivity('status_change', Auth::id(), $oldStatus, 'done', $validated['completion_notes'] ?? null);
            } elseif ($validated['status'] === 'new' && $oldStatus === 'rejected') {
                // إعادة فتح
                $task->rejection_reason = null;
                $task->rejected_by = null;
                $task->rejected_at = null;
                $task->logActivity('reopen', Auth::id(), $oldStatus, 'new', 'تم إعادة فتح المهمة');
            } else {
                $task->logActivity('status_change', Auth::id(), $oldStatus, $validated['status']);
            }

            $task->save();
            $task->load(['project', 'assignee', 'projectStage']);

            DB::commit();

            // التحقق من طلب AJAX
            if ($request->expectsJson() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث حالة المهمة بنجاح',
                    'task' => [
                        'id' => $task->id,
                        'status' => $task->status,
                        'title' => $task->title,
                    ]
                ], 200);
            }

            return back()->with('success', 'تم تحديث حالة المهمة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMsg = 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage();
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 500);
            }
            return back()->with('error', $errorMsg);
        }
    }

    /**
     * Add comment/note to task.
     */
    public function comment(Request $request, string $id)
    {
        $task = Task::findOrFail($id);
        Gate::authorize('addNote', $task);

        $validated = $request->validate([
            'notes' => 'required|string',
        ], [
            'notes.required' => 'الملاحظة مطلوبة',
        ]);

        DB::beginTransaction();
        try {
            $taskNote = $task->logActivity('comment', Auth::id(), null, null, $validated['notes']);

            // إرسال إشعار للتعليق
            if ($taskNote) {
                TaskCommented::dispatch($task, $taskNote, Auth::id());
            }

            DB::commit();

            // التحقق من طلب AJAX
            if ($request->expectsJson() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إضافة الملاحظة بنجاح',
                ], 200);
            }

            return back()->with('success', 'تم إضافة الملاحظة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMsg = 'حدث خطأ أثناء إضافة الملاحظة: ' . $e->getMessage();
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 500);
            }
            return back()->with('error', $errorMsg);
        }
    }
}
