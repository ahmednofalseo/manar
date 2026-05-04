<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkflowTemplateRequest;
use App\Models\Service;
use App\Models\WorkflowStep;
use App\Models\WorkflowTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WorkflowTemplatesController extends Controller
{
    /**
     * عرض قوالب المسارات
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', WorkflowTemplate::class);

        $query = WorkflowTemplate::with(['service', 'steps'])->latest();

        // فلترة حسب الخدمة
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        $templates = $query->paginate(20);
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('workflow-templates.index', compact('templates', 'services'));
    }

    /**
     * عرض نموذج إنشاء قالب
     */
    public function create(Request $request)
    {
        Gate::authorize('create', WorkflowTemplate::class);

        $serviceId = $request->get('service_id');
        $services = Service::where('is_active', true)->orderBy('name')->get();

        $departments = [
            'معماري' => __('Dept architectural'),
            'إنشائي' => __('Dept structural'),
            'كهربائي' => __('Dept electrical'),
            'ميكانيكي' => __('Dept mechanical'),
            'مساحي' => __('Dept surveying'),
            'دفاع_مدني' => __('Dept civil defense'),
            'بلدي' => __('Dept municipal'),
            'أخرى' => __('Dept other'),
        ];

        return view('workflow-templates.create', compact('services', 'serviceId', 'departments'));
    }

    /**
     * حفظ قالب جديد
     */
    public function store(StoreWorkflowTemplateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $steps = $data['steps'];
            unset($data['steps']);

            // إنشاء القالب
            $template = WorkflowTemplate::create($data);

            // إنشاء الخطوات
            foreach ($steps as $stepData) {
                $stepData['workflow_template_id'] = $template->id;
                WorkflowStep::create($stepData);
            }

            DB::commit();

            return redirect()->route('workflow-templates.index')
                ->with('success', __('Workflow template created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', __('Workflow template create error', ['message' => $e->getMessage()]));
        }
    }

    /**
     * عرض تفاصيل قالب
     */
    public function show(WorkflowTemplate $workflowTemplate)
    {
        Gate::authorize('view', $workflowTemplate);

        $workflowTemplate->load(['service', 'steps']);

        return view('workflow-templates.show', compact('workflowTemplate'));
    }

    /**
     * عرض نموذج تعديل قالب
     */
    public function edit(WorkflowTemplate $workflowTemplate)
    {
        Gate::authorize('update', $workflowTemplate);

        $workflowTemplate->load('steps');
        $services = Service::where('is_active', true)->orderBy('name')->get();

        $departments = [
            'معماري' => __('Dept architectural'),
            'إنشائي' => __('Dept structural'),
            'كهربائي' => __('Dept electrical'),
            'ميكانيكي' => __('Dept mechanical'),
            'مساحي' => __('Dept surveying'),
            'دفاع_مدني' => __('Dept civil defense'),
            'بلدي' => __('Dept municipal'),
            'أخرى' => __('Dept other'),
        ];

        return view('workflow-templates.edit', compact('workflowTemplate', 'services', 'departments'));
    }

    /**
     * تحديث قالب
     */
    public function update(StoreWorkflowTemplateRequest $request, WorkflowTemplate $workflowTemplate)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $steps = $data['steps'] ?? [];
            unset($data['steps']);

            // تحديث القالب
            $workflowTemplate->update($data);

            // حذف الخطوات القديمة وإعادة إنشائها
            $workflowTemplate->steps()->delete();

            // إنشاء الخطوات الجديدة
            foreach ($steps as $stepData) {
                $stepData['workflow_template_id'] = $workflowTemplate->id;
                WorkflowStep::create($stepData);
            }

            DB::commit();

            return redirect()->route('workflow-templates.index')
                ->with('success', __('Workflow template updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', __('Workflow template update error', ['message' => $e->getMessage()]));
        }
    }

    /**
     * حذف قالب
     */
    public function destroy(WorkflowTemplate $workflowTemplate)
    {
        Gate::authorize('delete', $workflowTemplate);

        // التحقق من وجود مسارات مشاريع مرتبطة
        if ($workflowTemplate->projectWorkflows()->count() > 0) {
            return redirect()->route('workflow-templates.index')
                ->with('error', __('Workflow template cannot delete in use'));
        }

        $workflowTemplate->delete();

        return redirect()->route('workflow-templates.index')
            ->with('success', __('Workflow template deleted successfully'));
    }
}
