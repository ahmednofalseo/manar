<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $query = User::with('roles')->latest();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الدور
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role);
            });
        }

        // فلترة حسب المسمى الوظيفي
        if ($request->filled('job_title')) {
            $query->where('job_title', 'like', "%{$request->job_title}%");
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $users = $query->paginate(15);

        // الإحصائيات
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();
        $recentLogins = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();

        // الأدوار للفلترة
        $roles = Role::orderBy('display_name')->get();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'suspendedUsers',
            'recentLogins',
            'roles'
        ));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        Gate::authorize('create', User::class);

        $roles = Role::orderBy('display_name')->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // تشفير كلمة المرور
            $data['password'] = Hash::make($data['password']);

            // رفع الصورة الشخصية
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            // رفع ملف شهادة مزاولة المهنة
            if ($request->hasFile('practice_license_file')) {
                $data['practice_license_file'] = $request->file('practice_license_file')->store('practice-licenses', 'public');
            }

            // إنشاء المستخدم
            $user = User::create($data);

            // ربط الأدوار
            $user->roles()->sync($request->roles);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'تم إنشاء الموظف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::with(['roles', 'roles.permissions'])->findOrFail($id);

        Gate::authorize('view', $user);

        // جلب المشاريع التي المستخدم مدير عليها أو عضو في فريقها
        $userProjects = Project::where(function($q) use ($user) {
            $q->where('project_manager_id', $user->id)
              ->orWhereJsonContains('team_members', (string)$user->id)
              ->orWhereJsonContains('team_members', $user->id);
        })
        ->with(['client', 'projectManager', 'tasks' => function($q) use ($user) {
            $q->where('assignee_id', $user->id);
        }])
        ->latest()
        ->get();

        // جلب جميع المهام المسندة للمستخدم
        $assignedTasks = Task::where('assignee_id', $user->id)
            ->whereNotNull('project_id')
            ->with(['project', 'projectStage', 'assignee'])
            ->latest()
            ->get();

        // جلب المهام المتأخرة
        $overdueTasks = $assignedTasks->filter(function($task) {
            return $task->due_date && 
                   $task->due_date < now() && 
                   !in_array($task->status, ['done', 'rejected']);
        });

        // جلب المهام التي أنشأها المستخدم
        $createdTasks = Task::where('created_by', $user->id)
            ->whereNotNull('project_id')
            ->with(['project', 'projectStage', 'assignee'])
            ->latest()
            ->get();

        // إحصائيات الأداء
        $totalTasks = $assignedTasks->count();
        $completedTasks = $assignedTasks->where('status', 'done')->count();
        $inProgressTasks = $assignedTasks->where('status', 'in_progress')->count();
        $newTasks = $assignedTasks->where('status', 'new')->count();
        $rejectedTasks = $assignedTasks->where('status', 'rejected')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        // جلب سجل تسجيل الدخول من جدول sessions
        $loginLogs = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->limit(20)
            ->get()
            ->map(function($session) {
                return [
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => date('Y-m-d H:i:s', $session->last_activity),
                    'timestamp' => $session->last_activity,
                ];
            });

        // إحصائيات المشاريع
        $totalProjects = $userProjects->count();
        $activeProjects = $userProjects->where('status', 'قيد التنفيذ')->count();
        $completedProjects = $userProjects->where('status', 'مكتمل')->count();

        // حساب دور المستخدم في كل مشروع
        $projectRoles = [];
        foreach ($userProjects as $project) {
            if ($project->project_manager_id == $user->id) {
                $projectRoles[$project->id] = 'manager';
            } elseif (is_array($project->team_members) && in_array($user->id, $project->team_members)) {
                $projectRoles[$project->id] = 'team_member';
            } else {
                $projectRoles[$project->id] = 'member';
            }
        }

        return view('admin.users.show', compact(
            'user',
            'userProjects',
            'assignedTasks',
            'overdueTasks',
            'createdTasks',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'newTasks',
            'rejectedTasks',
            'completionRate',
            'loginLogs',
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'projectRoles'
        ));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $id)
    {
        $user = User::with('roles')->findOrFail($id);

        Gate::authorize('update', $user);

        $roles = Role::orderBy('display_name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);

        DB::beginTransaction();
        try {
            $data = $request->validated();

            // تحديث كلمة المرور إذا تم تقديمها
            if ($request->filled('password')) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // رفع الصورة الشخصية
            if ($request->hasFile('avatar')) {
                // حذف الصورة القديمة
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            // رفع ملف شهادة مزاولة المهنة
            if ($request->hasFile('practice_license_file')) {
                // حذف الملف القديم
                if ($user->practice_license_file) {
                    Storage::disk('public')->delete($user->practice_license_file);
                }
                $data['practice_license_file'] = $request->file('practice_license_file')->store('practice-licenses', 'public');
            }

            // تحديث المستخدم
            $user->update($data);

            // تحديث الأدوار
            $user->roles()->sync($request->roles);

            DB::commit();

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'تم تحديث بيانات الموظف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        Gate::authorize('delete', $user);

        DB::beginTransaction();
        try {
            // حذف الصورة الشخصية
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // حذف ملف شهادة مزاولة المهنة
            if ($user->practice_license_file) {
                Storage::disk('public')->delete($user->practice_license_file);
            }

            // فصل الأدوار قبل الحذف
            $user->roles()->detach();

            // حذف المستخدم
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'تم حذف الموظف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف الموظف: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (active/suspended).
     */
    public function toggle(string $id)
    {
        $user = User::findOrFail($id);

        Gate::authorize('update', $user);

        $user->status = $user->status === 'active' ? 'suspended' : 'active';
        $user->save();

        $statusText = $user->status === 'active' ? 'تفعيل' : 'تعطيل';
        return back()->with('success', "تم {$statusText} الموظف بنجاح");
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, string $id)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $user = User::findOrFail($id);

        Gate::authorize('update', $user);

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'تم إعادة ضبط كلمة المرور بنجاح');
    }

    /**
     * Update user roles and permissions.
     */
    public function updateRoles(Request $request, string $id)
    {
        $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ], [
            'roles.required' => 'يجب اختيار دور واحد على الأقل',
            'roles.array' => 'الأدوار يجب أن تكون مصفوفة',
            'roles.min' => 'يجب اختيار دور واحد على الأقل',
            'roles.*.exists' => 'أحد الأدوار المختارة غير موجود',
        ]);

        $user = User::findOrFail($id);

        Gate::authorize('update', $user);

        $user->roles()->sync($request->roles);

        return back()->with('success', 'تم تحديث الأدوار والصلاحيات بنجاح');
    }

    /**
     * Export users.
     */
    public function export(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        // TODO: Implement CSV/PDF export
        return back()->with('success', 'تم تصدير البيانات بنجاح');
    }

    /**
     * Import users from CSV.
     */
    public function import(Request $request)
    {
        Gate::authorize('create', User::class);

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ], [
            'file.required' => 'الملف مطلوب',
            'file.file' => 'يجب أن يكون ملف',
            'file.mimes' => 'نوع الملف يجب أن يكون CSV',
        ]);

        // TODO: Implement CSV import
        return back()->with('success', 'تم استيراد البيانات بنجاح');
    }
}
