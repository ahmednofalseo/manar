<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectWorkflow;
use App\Models\ProjectWorkflowStep;
use App\Models\Service;
use App\Models\WorkflowTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectWorkflowsController extends Controller
{
    /**
     * عرض مسارات مشروع
     */
    public function index(Project $project)
    {
        Gate::authorize('view', $project);

        $workflows = $project->workflows()->with(['service', 'steps.assignedUser'])->get();

        return view('projects.workflows.index', compact('project', 'workflows'));
    }

    /**
     * إنشاء مسار جديد لمشروع
     */
    public function create(Project $project)
    {
        Gate::authorize('update', $project);

        // جلب الخدمات الرئيسية فقط (ليست فرعية) مع الخدمات الفرعية مرتبة
        $services = Service::where('is_active', true)
            ->whereNull('parent_id') // الخدمات الرئيسية فقط
            ->orderByRaw('COALESCE(`order`, 999999) ASC') // ترتيب حسب order، إذا كان null يأتي في النهاية
            ->orderBy('name')
            ->with(['subServices' => function($query) {
                $query->where('is_active', true)
                      ->orderByRaw('COALESCE(`order`, 999999) ASC')
                      ->orderBy('name');
            }])
            ->get();
        
        $isParallel = request()->get('parallel', false);
        $parentWorkflowId = request()->get('parent_workflow_id');

        return view('projects.workflows.create', compact('project', 'services', 'isParallel', 'parentWorkflowId'));
    }

    /**
     * حفظ مسار جديد
     */
    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'workflow_template_id' => 'nullable|exists:workflow_templates,id',
            'name' => 'required|string|max:255',
            'is_parallel' => 'boolean',
            'parent_workflow_id' => 'nullable|exists:project_workflows,id',
            'start_date' => 'nullable|date',
        ], [
            'service_id.required' => 'الخدمة مطلوبة',
            'service_id.exists' => 'الخدمة المحددة غير موجودة',
            'name.required' => 'اسم المسار مطلوب',
        ]);

        DB::beginTransaction();
        try {
            $service = Service::findOrFail($validated['service_id']);
            
            // إذا لم يُحدد قالب، استخدم القالب الافتراضي
            if (empty($validated['workflow_template_id'])) {
                $template = $service->defaultWorkflowTemplate;
                if (!$template) {
                    throw new \Exception('لا يوجد قالب افتراضي للخدمة المحددة');
                }
                $validated['workflow_template_id'] = $template->id;
            }

            $template = WorkflowTemplate::with('steps')->findOrFail($validated['workflow_template_id']);

            // إنشاء المسار
            $workflow = ProjectWorkflow::create([
                'project_id' => $project->id,
                'service_id' => $validated['service_id'],
                'workflow_template_id' => $validated['workflow_template_id'],
                'name' => $validated['name'],
                'is_parallel' => $validated['is_parallel'] ?? false,
                'parent_workflow_id' => $validated['parent_workflow_id'] ?? null,
                'start_date' => $validated['start_date'] ?? Carbon::now(),
                'status' => 'active',
            ]);

            // إنشاء خطوات المسار من القالب
            $startDate = Carbon::parse($workflow->start_date);
            foreach ($template->steps as $templateStep) {
                $expectedEndDate = $startDate->copy()->addDays($templateStep->default_duration_days);
                
                ProjectWorkflowStep::create([
                    'project_workflow_id' => $workflow->id,
                    'workflow_step_id' => $templateStep->id,
                    'name' => $templateStep->name,
                    'description' => $templateStep->description,
                    'order' => $templateStep->order,
                    'department' => $templateStep->department,
                    'duration_days' => $templateStep->default_duration_days,
                    'expected_outputs' => $templateStep->expected_outputs,
                    'status' => 'pending',
                    'expected_end_date' => $expectedEndDate,
                    'is_custom' => false,
                ]);

                // إذا لم تكن متوازية، ابدأ الخطوة التالية بعد انتهاء السابقة
                if (!$templateStep->is_parallel) {
                    $startDate = $expectedEndDate->copy()->addDay();
                }
            }

            // حساب تاريخ الانتهاء المتوقع للمسار
            $lastStep = $workflow->steps()->orderBy('expected_end_date', 'desc')->first();
            if ($lastStep) {
                $workflow->expected_end_date = $lastStep->expected_end_date;
                $workflow->save();
            }

            DB::commit();

            return redirect()->route('projects.workflows.index', $project)
                ->with('success', 'تم إنشاء المسار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المسار: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل مسار
     */
    public function show(Project $project, ProjectWorkflow $workflow)
    {
        Gate::authorize('view', $project);

        $workflow->load(['service', 'steps.assignedUser', 'parallelWorkflows']);

        return view('projects.workflows.show', compact('project', 'workflow'));
    }

    /**
     * تحديث حالة خطوة
     */
    public function updateStepStatus(Request $request, Project $project, ProjectWorkflow $workflow, ProjectWorkflowStep $step)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,skipped,blocked',
        ]);

        $step->updateStatus($validated['status']);

        return back()->with('success', 'تم تحديث حالة الخطوة بنجاح');
    }

    /**
     * إضافة خطوة مخصصة
     */
    public function addCustomStep(Request $request, Project $project, ProjectWorkflow $workflow)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'department' => 'required|string|in:معماري,إنشائي,كهربائي,ميكانيكي,مساحي,دفاع_مدني,بلدي,أخرى',
            'duration_days' => 'required|integer|min:1',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $step = ProjectWorkflowStep::create([
            'project_workflow_id' => $workflow->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'order' => $validated['order'],
            'department' => $validated['department'],
            'duration_days' => $validated['duration_days'],
            'status' => 'pending',
            'assigned_to' => $validated['assigned_to'] ?? null,
            'is_custom' => true,
        ]);

        return back()->with('success', 'تم إضافة الخطوة بنجاح');
    }

    /**
     * حذف خطوة
     */
    public function deleteStep(Project $project, ProjectWorkflow $workflow, ProjectWorkflowStep $step)
    {
        Gate::authorize('update', $project);

        if (!$step->is_custom) {
            return back()->with('error', 'لا يمكن حذف خطوة من القالب، يمكنك تخطيها فقط');
        }

        $step->delete();
        $workflow->updateProgress();

        return back()->with('success', 'تم حذف الخطوة بنجاح');
    }

    /**
     * إعادة ترتيب الخطوات
     */
    public function reorderSteps(Request $request, Project $project, ProjectWorkflow $workflow)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'steps' => 'required|array',
            'steps.*' => 'required|exists:project_workflow_steps,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['steps'] as $order => $stepId) {
                ProjectWorkflowStep::where('id', $stepId)
                    ->where('project_workflow_id', $workflow->id)
                    ->update(['order' => $order]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة ترتيب الخطوات بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعادة الترتيب: ' . $e->getMessage()
            ], 500);
        }
    }
}
