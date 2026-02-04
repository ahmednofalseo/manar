<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStage;
use App\Models\ProjectAttachment;
use App\Models\ProjectThirdParty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Helpers\PermissionHelper;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Project::with([
            'projectManager', 
            'projectStages', 
            'tasks' => function($q) {
                $q->whereIn('status', ['new', 'in_progress']);
            },
            'attachments' // إضافة attachments لتجنب N+1 في project-card
        ]);

        // تطبيق فلاتر الصلاحيات - المستخدم يرى المشاريع المخصصة له فقط
        if (!$user->hasRole('super_admin')) {
            // للمستخدمين العاديين: إخفاء المشاريع المخفية + يروا المشاريع التي هم مديرين عليها أو أعضاء في فريقها
            $query->where('is_hidden', false); // إخفاء المشاريع المخفية للمستخدمين العاديين فقط
            $query->where(function($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                  ->orWhereJsonContains('team_members', (string)$user->id)
                  ->orWhereJsonContains('team_members', $user->id);
            });
        }
        // Super Admin يرى الكل (بما في ذلك المشاريع المخفية - لا فلاتر)

        // الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('project_number', 'like', "%{$search}%")
                  ->orWhere('owner', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('owner')) {
            $query->where('owner', 'like', "%{$request->owner}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('current_stage')) {
            $query->where('current_stage', $request->current_stage);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // KPIs (optimized - single query for counts)
        // Note: Using 'delayed_count' instead of 'delayed' as it's a MySQL reserved word
        // تطبيق نفس فلاتر الصلاحيات على KPIs
        $kpisQuery = Project::query();
        if (!$user->hasRole('super_admin')) {
            // للمستخدمين العاديين: إخفاء المشاريع المخفية
            $kpisQuery->where('is_hidden', false);
            $kpisQuery->where(function($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                  ->orWhereJsonContains('team_members', (string)$user->id)
                  ->orWhereJsonContains('team_members', $user->id);
            });
        }
        // Super Admin يرى الكل (بما في ذلك المشاريع المخفية)
        $kpis = $kpisQuery->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "قيد التنفيذ" THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN status = "مكتمل" THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = "متوقف" THEN 1 ELSE 0 END) as delayed_count,
            SUM(value) as total_value
        ')->first();
        
        $totalProjects = $kpis->total ?? 0;
        $activeProjects = $kpis->active ?? 0;
        $completedProjects = $kpis->completed ?? 0;
        $delayedProjects = $kpis->delayed_count ?? 0;
        $totalValue = $kpis->total_value ?? 0;

        $projects = $query->latest()->paginate(12);

        return view('projects.index', compact(
            'projects',
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'delayedProjects',
            'totalValue'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Project::class);
        // جلب مديري المشاريع (project_manager)
        $projectManagers = User::whereHas('roles', function($q) {
            $q->where('name', 'project_manager');
        })->where('status', 'active')->orderBy('name')->get();

        // جلب المهندسين (engineer + project_manager)
        $engineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['engineer', 'project_manager']);
        })->where('status', 'active')->orderBy('name')->get();

        // إذا لم يوجد مستخدمون بأدوار محددة، جلب جميع المستخدمين النشطين
        if ($projectManagers->isEmpty()) {
            $projectManagers = User::where('status', 'active')->orderBy('name')->get();
        }

        if ($engineers->isEmpty()) {
            $engineers = User::where('status', 'active')->orderBy('name')->get();
        }

        $clients = \App\Models\Client::where('status', 'active')->orderBy('name')->get();
        $selectedClientId = $request->get('client_id');

        return view('projects.create', compact('projectManagers', 'engineers', 'clients', 'selectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Project::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_number' => 'nullable|string|unique:projects,project_number',
            'type' => 'required|string',
            'city' => 'required|string',
            'district' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'owner' => 'nullable|string|max:255',
            'value' => 'required|numeric|min:0',
            'installments_count' => 'nullable|integer|min:1|max:100',
            'contract_number' => 'nullable|string',
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'land_number' => 'nullable|string',
            'land_code' => 'nullable|string',
            'plan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg|max:10240',
            'baladi_request_number' => 'nullable|string',
            'stages' => 'nullable|array',
            'stages.*' => 'string',
            'project_manager_id' => 'nullable|exists:users,id',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'internal_notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'name.required' => 'اسم المشروع مطلوب',
            'type.required' => 'نوع المشروع مطلوب',
            'city.required' => 'المدينة مطلوبة',
            'value.required' => 'قيمة المشروع مطلوبة',
            'value.numeric' => 'قيمة المشروع يجب أن تكون رقماً',
            'value.min' => 'قيمة المشروع يجب أن تكون أكبر من أو تساوي 0',
            'client_id.exists' => 'العميل المحدد غير موجود',
            'project_manager_id.exists' => 'مدير المشروع المحدد غير موجود',
            'team_members.*.exists' => 'أحد أعضاء الفريق المحددين غير موجود',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء',
        ]);

        DB::beginTransaction();
        try {
            // توليد رقم المشروع إذا لم يتم إدخاله
            if (empty($validated['project_number'])) {
                $validated['project_number'] = Project::generateProjectNumber();
            }

            // رفع الملفات
            if ($request->hasFile('contract_file')) {
                $validated['contract_file'] = $request->file('contract_file')->store('contracts', 'public');
            }

            if ($request->hasFile('plan_file')) {
                $validated['plan_file'] = $request->file('plan_file')->store('plans', 'public');
            }

            // الحالة الافتراضية
            $validated['status'] = 'قيد التنفيذ';
            $validated['progress'] = 0;
            
            // القيمة الافتراضية لعدد الأقساط
            if (!isset($validated['installments_count']) || empty($validated['installments_count'])) {
                $validated['installments_count'] = 1;
            }

            // إنشاء المشروع
            $project = Project::create($validated);

            // إضافة المراحل
            if ($request->filled('stages')) {
                foreach ($request->stages as $stageName) {
                    ProjectStage::create([
                        'project_id' => $project->id,
                        'stage_name' => $stageName,
                        'status' => 'جديد',
                        'progress' => 0,
                    ]);
                }
                // تحديد المرحلة الحالية الأولى
                $project->update(['current_stage' => $request->stages[0] ?? null]);
            }

            // إضافة أعضاء الفريق
            if ($request->filled('team_members')) {
                $project->teamUsers()->sync($request->team_members);
            }

            // إضافة الأطراف الثالثة
            if ($request->filled('third_party') && is_array($request->third_party)) {
                foreach ($request->third_party as $thirdParty) {
                    if (is_array($thirdParty) && !empty($thirdParty['name'])) {
                        ProjectThirdParty::create([
                            'project_id' => $project->id,
                            'name' => $thirdParty['name'],
                            'date' => $thirdParty['date'] ?? null,
                            'notes' => $thirdParty['notes'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'تم إنشاء المشروع بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating project: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء المشروع: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with([
            'projectManager',
            'teamUsers',
            'projectStages' => function($query) {
                $query->orderBy('created_at');
            },
            'projectStages.tasks.assignee',
            'attachments' => function($query) {
                $query->with('uploader')->latest();
            },
            'thirdParties' => function($query) {
                $query->latest();
            },
            'tasks.assignee',
            'tasks.projectStage',
            'workflows.service',
            'workflows.steps.assignedUser',
            'documents.creator',
            'documents.approver',
        ])->findOrFail($id);

        Gate::authorize('view', $project);

        // حساب التقدم التلقائي
        $project->progress = $project->calculateProgress();
        $project->save();

        // إحصائيات المشروع (استخدام العلاقات المحملة مسبقاً)
        $tasksCount = $project->tasks ? $project->tasks->count() : 0;
        $stagesCount = $project->projectStages ? $project->projectStages->count() : 0;
        $attachmentsCount = $project->attachments ? $project->attachments->count() : 0;

        return view('projects.show', compact('project', 'tasksCount', 'stagesCount', 'attachmentsCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::with(['teamUsers', 'projectStages', 'thirdParties'])->findOrFail($id);
        Gate::authorize('update', $project);

        $projectManagers = User::whereHas('roles', function($q) {
            $q->where('name', 'project_manager');
        })->where('status', 'active')->orderBy('name')->get();

        $engineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['engineer', 'project_manager']);
        })->where('status', 'active')->orderBy('name')->get();

        // إذا لم يوجد مستخدمون بأدوار محددة، جلب جميع المستخدمين النشطين
        if ($projectManagers->isEmpty()) {
            $projectManagers = User::where('status', 'active')->orderBy('name')->get();
        }

        if ($engineers->isEmpty()) {
            $engineers = User::where('status', 'active')->orderBy('name')->get();
        }

        $clients = \App\Models\Client::where('status', 'active')->orderBy('name')->get();

        return view('projects.edit', compact('project', 'projectManagers', 'engineers', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_number' => 'nullable|string|unique:projects,project_number,' . $id,
            'type' => 'required|string',
            'city' => 'required|string',
            'district' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'owner' => 'nullable|string|max:255',
            'value' => 'required|numeric|min:0',
            'contract_number' => 'nullable|string',
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'land_number' => 'nullable|string',
            'land_code' => 'nullable|string',
            'plan_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg|max:10240',
            'baladi_request_number' => 'nullable|string',
            'stages' => 'nullable|array',
            'stages.*' => 'string',
            'status' => 'required|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'current_stage' => 'nullable|string',
            'project_manager_id' => 'nullable|exists:users,id',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'internal_notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'name.required' => 'اسم المشروع مطلوب',
            'type.required' => 'نوع المشروع مطلوب',
            'city.required' => 'المدينة مطلوبة',
            'owner.required' => 'اسم المالك مطلوب',
            'client_id.exists' => 'العميل المحدد غير موجود',
            'value.required' => 'قيمة المشروع مطلوبة',
        ]);

        DB::beginTransaction();
        try {
            // رفع الملفات الجديدة
            if ($request->hasFile('contract_file')) {
                if ($project->contract_file) {
                    Storage::disk('public')->delete($project->contract_file);
                }
                $validated['contract_file'] = $request->file('contract_file')->store('contracts', 'public');
            }

            if ($request->hasFile('plan_file')) {
                if ($project->plan_file) {
                    Storage::disk('public')->delete($project->plan_file);
                }
                $validated['plan_file'] = $request->file('plan_file')->store('plans', 'public');
            }

            // تحديث المشروع
            $oldStatus = $project->status;
            $project->update($validated);

            // إغلاق الشات إذا المشروع أصبح مكتمل
            if ($validated['status'] === 'مكتمل' && $oldStatus !== 'مكتمل') {
                $conversation = \App\Models\Conversation::where('type', 'project')
                    ->where('project_id', $project->id)
                    ->first();
                
                if ($conversation && !$conversation->is_closed) {
                    $conversation->close();
                }
            }

            // تحديث المراحل
            if ($request->filled('stages')) {
                // حذف المراحل القديمة غير المحددة
                $project->projectStages()->whereNotIn('stage_name', $request->stages)->delete();
                
                // إضافة المراحل الجديدة
                foreach ($request->stages as $stageName) {
                    ProjectStage::firstOrCreate(
                        [
                            'project_id' => $project->id,
                            'stage_name' => $stageName,
                        ],
                        [
                            'status' => 'جديد',
                            'progress' => 0,
                        ]
                    );
                }
            }

            // تحديث أعضاء الفريق
            if ($request->filled('team_members')) {
                $project->teamUsers()->sync($request->team_members);
            }

            DB::commit();

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'تم تحديث المشروع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث المشروع: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        Gate::authorize('delete', $project);

        // حذف الملفات
        if ($project->contract_file) {
            Storage::disk('public')->delete($project->contract_file);
        }
        if ($project->plan_file) {
            Storage::disk('public')->delete($project->plan_file);
        }

        // حذف المرفقات
        foreach ($project->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'تم حذف المشروع بنجاح');
    }
    
    /**
     * إخفاء/إظهار المشروع
     */
    public function toggleHide(string $id)
    {
        $project = Project::findOrFail($id);
        
        // التحقق من الصلاحيات - فقط super_admin أو project_manager يمكنه إخفاء المشروع
        if (!$project->project_manager_id || $project->project_manager_id !== Auth::id()) {
            if (!Auth::user()->hasRole('super_admin')) {
                abort(403, 'غير مصرح لك بإخفاء هذا المشروع');
            }
        }
        
        $project->update([
            'is_hidden' => !$project->is_hidden,
        ]);
        
        $message = $project->is_hidden 
            ? 'تم إخفاء المشروع بنجاح' 
            : 'تم إظهار المشروع بنجاح';
        
        return redirect()->route('projects.index')
            ->with('success', $message);
    }

    /**
     * رفع مرفق للمشروع
     */
    public function storeAttachment(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'category' => 'nullable|string',
            'name' => 'nullable|string',
        ], [
            'file.required' => 'الملف مطلوب',
            'file.file' => 'يجب أن يكون ملف صحيح',
            'file.max' => 'حجم الملف يجب أن يكون أقل من 10 ميجابايت',
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->store('project-attachments', 'public');

            ProjectAttachment::create([
                'project_id' => $project->id,
                'name' => $validated['name'] ?? $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'category' => $validated['category'] ?? 'عام',
                'uploaded_by' => auth()->id(),
            ]);

            return back()->with('success', 'تم رفع المرفق بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage());
        }
    }

    /**
     * حذف مرفق من المشروع
     */
    public function destroyAttachment(Request $request, string $projectId, string $attachmentId)
    {
        $project = Project::findOrFail($projectId);
        $attachment = ProjectAttachment::where('project_id', $project->id)
            ->findOrFail($attachmentId);

        try {
            // حذف الملف من التخزين
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();

            return back()->with('success', 'تم حذف المرفق بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage());
        }
    }

    /**
     * تحديث مرحلة المشروع
     */
    public function updateStage(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'stage_name' => 'required|string',
            'status' => 'required|in:جديد,جارٍ,مكتمل',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $stage = $project->projectStages()->where('stage_name', $validated['stage_name'])->first();

        if ($stage) {
            $stage->update($validated);
            
            // تحديث المرحلة الحالية
            if ($validated['status'] == 'جارٍ') {
                $project->update(['current_stage' => $validated['stage_name']]);
            }

            // إعادة حساب التقدم
            $project->progress = $project->calculateProgress();
            $project->save();
        }

        return back()->with('success', 'تم تحديث المرحلة بنجاح');
    }

    /**
     * إضافة طرف ثالث
     */
    public function storeThirdParty(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        ProjectThirdParty::create([
            'project_id' => $project->id,
            'name' => $validated['name'],
            'date' => $validated['date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم إضافة الطرف الثالث بنجاح');
    }

    /**
     * عرض الماليات للمشروع
     */
    public function financialsIndex(string $id)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.view') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $project = Project::findOrFail($id);
        return redirect()->route('financials.index', ['project_id' => $project->id]);
    }

    /**
     * إنشاء فاتورة للمشروع
     */
    public function storeInvoice(Request $request, string $id)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.create') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $project = Project::findOrFail($id);
        return redirect()->route('financials.create', ['project_id' => $project->id]);
    }
}
