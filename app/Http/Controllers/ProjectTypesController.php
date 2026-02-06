<?php

namespace App\Http\Controllers;

use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class ProjectTypesController extends Controller
{
    /**
     * عرض قائمة أنواع المشاريع
     */
    public function index()
    {
        Gate::authorize('viewAny', ProjectType::class);

        $types = ProjectType::ordered()->get();

        return view('settings.project-types.index', compact('types'));
    }

    /**
     * عرض نموذج إنشاء نوع مشروع
     */
    public function create()
    {
        Gate::authorize('create', ProjectType::class);

        return view('settings.project-types.create');
    }

    /**
     * حفظ نوع مشروع جديد
     */
    public function store(Request $request)
    {
        Gate::authorize('create', ProjectType::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_types,name',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ], [
            'name.required' => 'اسم النوع مطلوب',
            'name.unique' => 'هذا النوع موجود بالفعل',
        ]);

        ProjectType::create($validated);

        return redirect()->route('settings.project-types.index')
            ->with('success', 'تم إنشاء نوع المشروع بنجاح');
    }

    /**
     * عرض نوع مشروع
     */
    public function show(ProjectType $projectType)
    {
        Gate::authorize('view', $projectType);

        return view('settings.project-types.show', compact('projectType'));
    }

    /**
     * عرض نموذج تعديل نوع مشروع
     */
    public function edit(ProjectType $projectType)
    {
        Gate::authorize('update', $projectType);

        return view('settings.project-types.edit', compact('projectType'));
    }

    /**
     * تحديث نوع مشروع
     */
    public function update(Request $request, ProjectType $projectType)
    {
        Gate::authorize('update', $projectType);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_types,name,' . $projectType->id,
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ], [
            'name.required' => 'اسم النوع مطلوب',
            'name.unique' => 'هذا النوع موجود بالفعل',
        ]);

        $projectType->update($validated);

        return redirect()->route('settings.project-types.index')
            ->with('success', 'تم تحديث نوع المشروع بنجاح');
    }

    /**
     * حذف نوع مشروع
     */
    public function destroy(ProjectType $projectType)
    {
        Gate::authorize('delete', $projectType);

        DB::beginTransaction();
        try {
            // التحقق من وجود مشاريع تستخدم هذا النوع
            $projectsCount = \App\Models\Project::where('type', $projectType->name)->count();
            
            if ($projectsCount > 0) {
                return back()->with('error', 'لا يمكن حذف هذا النوع لأنه مستخدم في ' . $projectsCount . ' مشروع');
            }

            $projectType->delete();

            DB::commit();

            return redirect()->route('settings.project-types.index')
                ->with('success', 'تم حذف نوع المشروع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف نوع المشروع: ' . $e->getMessage());
        }
    }
}
