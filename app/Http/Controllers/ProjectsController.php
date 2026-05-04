<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;
use App\Models\City;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectStage;
use App\Models\ProjectThirdParty;
use App\Models\ProjectType;
use App\Models\StageSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProjectsController extends Controller
{
    /**
     * @return array{projectTypes: \Illuminate\Database\Eloquent\Collection<int, ProjectType>, stages: \Illuminate\Database\Eloquent\Collection<int, StageSetting>, typeLabelMap: array<string, string>, stageLabelMap: array<string, string>, statusLabelMap: array<string, string>}
     */
    protected function projectTypeStageLabelContext(): array
    {
        $projectTypes = ProjectType::active()->ordered()->get();
        $stages = StageSetting::active()->ordered()->get();

        return [
            'projectTypes' => $projectTypes,
            'stages' => $stages,
            'typeLabelMap' => $projectTypes->mapWithKeys(fn (ProjectType $t) => [$t->name => $t->display_name])->all(),
            'stageLabelMap' => $stages->mapWithKeys(fn (StageSetting $s) => [$s->name => $s->display_name])->all(),
            'statusLabelMap' => [
                'قيد التنفيذ' => __('In Progress'),
                'مكتمل' => __('Completed'),
                'متوقف' => __('Paused'),
                'ملغي' => __('Cancelled'),
            ],
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Project::with([
            'projectManager',
            'projectStages',
            'attachments',
        ])->withCount([
            'tasks',
            'invoices',
            'tasks as incomplete_tasks_count' => function ($q) {
                $q->whereNotIn('status', ['done', 'rejected']);
            },
        ]);

        // تطبيق فلاتر الصلاحيات - المستخدم يرى المشاريع المخصصة له فقط
        if (! $user->hasRole('super_admin')) {
            // للمستخدمين العاديين: إخفاء المشاريع المخفية + يروا المشاريع التي هم مديرين عليها أو أعضاء في فريقها
            $query->where('is_hidden', false); // إخفاء المشاريع المخفية للمستخدمين العاديين فقط
            $query->where(function ($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                    ->orWhereJsonContains('team_members', (string) $user->id)
                    ->orWhereJsonContains('team_members', $user->id);
            });
        }
        // Super Admin يرى الكل (بما في ذلك المشاريع المخفية - لا فلاتر)

        // الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('project_number', 'like', "%{$search}%")
                    ->orWhere('owner', 'like', "%{$search}%")
                    ->orWhere('owner_en', 'like', "%{$search}%");
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
            $statusFilter = $request->status;
            $projectStatusValues = ['قيد التنفيذ', 'مكتمل', 'متوقف', 'ملغي'];
            if (in_array($statusFilter, $projectStatusValues, true)) {
                $query->where('status', $statusFilter);
            } else {
                $query->where('current_stage', $statusFilter);
            }
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
        if (! $user->hasRole('super_admin')) {
            // للمستخدمين العاديين: إخفاء المشاريع المخفية
            $kpisQuery->where('is_hidden', false);
            $kpisQuery->where(function ($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                    ->orWhereJsonContains('team_members', (string) $user->id)
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

        $cities = City::active()->ordered()->get();
        $labelCtx = $this->projectTypeStageLabelContext();

        $districtsQuery = Project::query();
        $ownersQuery = Project::query();
        if (! $user->hasRole('super_admin')) {
            $districtsQuery->where('is_hidden', false)->where(function ($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                    ->orWhereJsonContains('team_members', (string) $user->id)
                    ->orWhereJsonContains('team_members', $user->id);
            });
            $ownersQuery->where('is_hidden', false)->where(function ($q) use ($user) {
                $q->where('project_manager_id', $user->id)
                    ->orWhereJsonContains('team_members', (string) $user->id)
                    ->orWhereJsonContains('team_members', $user->id);
            });
        }
        $districts = $districtsQuery->whereNotNull('district')->where('district', '!=', '')->distinct()->orderBy('district')->pluck('district');
        $owners = $ownersQuery->whereNotNull('owner')->where('owner', '!=', '')->distinct()->orderBy('owner')->pluck('owner');

        return view('projects.index', array_merge(compact(
            'projects',
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'delayedProjects',
            'totalValue',
            'cities',
            'districts',
            'owners',
        ), $labelCtx));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Project::class);
        // جلب مديري المشاريع (project_manager)
        $projectManagers = User::whereHas('roles', function ($q) {
            $q->where('name', 'project_manager');
        })->where('status', 'active')->orderBy('name')->get();

        // جلب المهندسين (engineer + project_manager)
        $engineers = User::whereHas('roles', function ($q) {
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

        // جلب أنواع المشاريع والمدن والمراحل من قاعدة البيانات
        $projectTypes = \App\Models\ProjectType::active()->ordered()->get();
        $cities = \App\Models\City::active()->ordered()->get();
        $stages = \App\Models\StageSetting::active()->ordered()->get();

        return view('projects.create', compact('projectManagers', 'engineers', 'clients', 'selectedClientId', 'projectTypes', 'cities', 'stages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Project::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'project_number' => 'nullable|string|unique:projects,project_number',
            'type' => 'required|string',
            'city' => 'required|string',
            'district' => 'nullable|string',
            'district_en' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'owner' => 'nullable|string|max:255',
            'owner_en' => 'nullable|string|max:255',
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
            'name.required' => __('Project name is required'),
            'type.required' => __('Project type is required'),
            'city.required' => __('City is required'),
            'value.required' => __('Project value is required'),
            'value.numeric' => __('Project value must be a number'),
            'value.min' => __('Project value must be at least zero'),
            'client_id.exists' => __('The selected client is invalid'),
            'project_manager_id.exists' => __('The selected project manager is invalid'),
            'team_members.*.exists' => __('A selected team member is invalid'),
            'end_date.after_or_equal' => __('End date must be on or after start date'),
        ]);

        foreach (['name_en', 'owner_en', 'district_en'] as $field) {
            $validated[$field] = filled($validated[$field] ?? null) ? $validated[$field] : null;
        }

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
            if (! isset($validated['installments_count']) || empty($validated['installments_count'])) {
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
                    if (is_array($thirdParty) && ! empty($thirdParty['name'])) {
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
                ->with('success', __('Project created successfully'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating project: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return back()->withInput()->with('error', __('An error occurred while creating the project'));
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
            'projectStages' => function ($query) {
                $query->orderBy('created_at');
            },
            'projectStages.tasks.assignee',
            'attachments' => function ($query) {
                $query->with('uploader')->latest();
            },
            'thirdParties' => function ($query) {
                $query->latest();
            },
            'tasks.assignee',
            'tasks.projectStage',
            'tasks.notes.user',
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

        $labelCtx = $this->projectTypeStageLabelContext();

        return view('projects.show', array_merge(
            compact('project', 'tasksCount', 'stagesCount', 'attachmentsCount'),
            Arr::only($labelCtx, ['typeLabelMap', 'stageLabelMap', 'statusLabelMap'])
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::with(['teamUsers', 'projectStages', 'thirdParties'])->findOrFail($id);
        Gate::authorize('update', $project);

        $projectManagers = User::whereHas('roles', function ($q) {
            $q->where('name', 'project_manager');
        })->where('status', 'active')->orderBy('name')->get();

        $engineers = User::whereHas('roles', function ($q) {
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

        // جلب أنواع المشاريع والمدن والمراحل من قاعدة البيانات
        $projectTypes = \App\Models\ProjectType::active()->ordered()->get();
        $cities = \App\Models\City::active()->ordered()->get();
        $stages = \App\Models\StageSetting::active()->ordered()->get();

        return view('projects.edit', compact('project', 'projectManagers', 'engineers', 'clients', 'projectTypes', 'cities', 'stages'));
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
            'name_en' => 'nullable|string|max:255',
            'project_number' => 'nullable|string|unique:projects,project_number,'.$id,
            'type' => 'required|string',
            'city' => 'required|string',
            'district' => 'nullable|string',
            'district_en' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'owner' => 'nullable|string|max:255',
            'owner_en' => 'nullable|string|max:255',
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

        foreach (['name_en', 'owner_en', 'district_en'] as $field) {
            $validated[$field] = filled($validated[$field] ?? null) ? $validated[$field] : null;
        }

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

                if ($conversation && ! $conversation->is_closed) {
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

            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث المشروع: '.$e->getMessage());
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
        if (! $project->project_manager_id || $project->project_manager_id !== Auth::id()) {
            if (! Auth::user()->hasRole('super_admin')) {
                abort(403, 'غير مصرح لك بإخفاء هذا المشروع');
            }
        }

        $project->update([
            'is_hidden' => ! $project->is_hidden,
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
            return back()->with('error', 'حدث خطأ أثناء رفع الملف: '.$e->getMessage());
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
            return back()->with('error', 'حدث خطأ أثناء حذف الملف: '.$e->getMessage());
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
        if (! PermissionHelper::hasPermission('financials.view') && ! PermissionHelper::hasPermission('financials.manage')) {
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
        if (! PermissionHelper::hasPermission('financials.create') && ! PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $project = Project::findOrFail($id);

        return redirect()->route('financials.create', ['project_id' => $project->id]);
    }
}
