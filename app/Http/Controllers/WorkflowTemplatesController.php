<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkflowTemplateRequest;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowStep;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

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
            'معماري' => 'معماري',
            'إنشائي' => 'إنشائي',
            'كهربائي' => 'كهربائي',
            'ميكانيكي' => 'ميكانيكي',
            'مساحي' => 'مساحي',
            'دفاع_مدني' => 'دفاع مدني',
            'بلدي' => 'بلدي',
            'أخرى' => 'أخرى',
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
                ->with('success', 'تم إنشاء قالب المسار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء القالب: ' . $e->getMessage());
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
            'معماري' => 'معماري',
            'إنشائي' => 'إنشائي',
            'كهربائي' => 'كهربائي',
            'ميكانيكي' => 'ميكانيكي',
            'مساحي' => 'مساحي',
            'دفاع_مدني' => 'دفاع مدني',
            'بلدي' => 'بلدي',
            'أخرى' => 'أخرى',
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
                ->with('success', 'تم تحديث قالب المسار بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث القالب: ' . $e->getMessage());
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
                ->with('error', 'لا يمكن حذف القالب لوجود مسارات مشاريع مرتبطة به');
        }

        $workflowTemplate->delete();

        return redirect()->route('workflow-templates.index')
            ->with('success', 'تم حذف القالب بنجاح');
    }
}
