<?php

namespace App\Http\Controllers;

use App\Models\StageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class ProjectStagesController extends Controller
{
    /**
     * عرض قائمة المراحل
     */
    public function index()
    {
        Gate::authorize('viewAny', StageSetting::class);

        $stages = StageSetting::ordered()->get();

        return view('settings.project-stages.index', compact('stages'));
    }

    /**
     * عرض نموذج إنشاء مرحلة
     */
    public function create()
    {
        Gate::authorize('create', StageSetting::class);

        return view('settings.project-stages.create');
    }

    /**
     * حفظ مرحلة جديدة
     */
    public function store(Request $request)
    {
        Gate::authorize('create', StageSetting::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stage_settings,name',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ], [
            'name.required' => 'اسم المرحلة مطلوب',
            'name.unique' => 'هذه المرحلة موجودة بالفعل',
        ]);

        StageSetting::create($validated);

        return redirect()->route('settings.project-stages.index')
            ->with('success', 'تم إنشاء المرحلة بنجاح');
    }

    /**
     * عرض مرحلة
     */
    public function show(StageSetting $projectStage)
    {
        Gate::authorize('view', $projectStage);

        return view('settings.project-stages.show', compact('projectStage'));
    }

    /**
     * عرض نموذج تعديل مرحلة
     */
    public function edit(StageSetting $projectStage)
    {
        Gate::authorize('update', $projectStage);

        return view('settings.project-stages.edit', compact('projectStage'));
    }

    /**
     * تحديث مرحلة
     */
    public function update(Request $request, StageSetting $projectStage)
    {
        Gate::authorize('update', $projectStage);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stage_settings,name,' . $projectStage->id,
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ], [
            'name.required' => 'اسم المرحلة مطلوب',
            'name.unique' => 'هذه المرحلة موجودة بالفعل',
        ]);

        $projectStage->update($validated);

        return redirect()->route('settings.project-stages.index')
            ->with('success', 'تم تحديث المرحلة بنجاح');
    }

    /**
     * حذف مرحلة
     */
    public function destroy(StageSetting $projectStage)
    {
        Gate::authorize('delete', $projectStage);

        DB::beginTransaction();
        try {
            // التحقق من وجود مشاريع تستخدم هذه المرحلة
            $projectsCount = \App\Models\Project::whereJsonContains('stages', $projectStage->name)->count();
            
            if ($projectsCount > 0) {
                return back()->with('error', 'لا يمكن حذف هذه المرحلة لأنها مستخدمة في ' . $projectsCount . ' مشروع');
            }

            $projectStage->delete();

            DB::commit();

            return redirect()->route('settings.project-stages.index')
                ->with('success', 'تم حذف المرحلة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف المرحلة: ' . $e->getMessage());
        }
    }
}
