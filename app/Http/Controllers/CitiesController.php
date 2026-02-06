<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class CitiesController extends Controller
{
    /**
     * عرض قائمة المدن
     */
    public function index()
    {
        Gate::authorize('viewAny', City::class);

        $cities = City::ordered()->get();

        return view('settings.cities.index', compact('cities'));
    }

    /**
     * عرض نموذج إنشاء مدينة
     */
    public function create()
    {
        Gate::authorize('create', City::class);

        return view('settings.cities.create');
    }

    /**
     * حفظ مدينة جديدة
     */
    public function store(Request $request)
    {
        Gate::authorize('create', City::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:cities,code',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ], [
            'name.required' => 'اسم المدينة مطلوب',
            'name.unique' => 'هذه المدينة موجودة بالفعل',
            'code.unique' => 'هذا الكود مستخدم بالفعل',
        ]);

        City::create($validated);

        return redirect()->route('settings.cities.index')
            ->with('success', 'تم إنشاء المدينة بنجاح');
    }

    /**
     * عرض مدينة
     */
    public function show(City $city)
    {
        Gate::authorize('view', $city);

        return view('settings.cities.show', compact('city'));
    }

    /**
     * عرض نموذج تعديل مدينة
     */
    public function edit(City $city)
    {
        Gate::authorize('update', $city);

        return view('settings.cities.edit', compact('city'));
    }

    /**
     * تحديث مدينة
     */
    public function update(Request $request, City $city)
    {
        Gate::authorize('update', $city);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:cities,code,' . $city->id,
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ], [
            'name.required' => 'اسم المدينة مطلوب',
            'name.unique' => 'هذه المدينة موجودة بالفعل',
            'code.unique' => 'هذا الكود مستخدم بالفعل',
        ]);

        $city->update($validated);

        return redirect()->route('settings.cities.index')
            ->with('success', 'تم تحديث المدينة بنجاح');
    }

    /**
     * حذف مدينة
     */
    public function destroy(City $city)
    {
        Gate::authorize('delete', $city);

        DB::beginTransaction();
        try {
            // التحقق من وجود مشاريع في هذه المدينة
            $projectsCount = \App\Models\Project::where('city', $city->name)->count();
            
            if ($projectsCount > 0) {
                return back()->with('error', 'لا يمكن حذف هذه المدينة لأنها مستخدمة في ' . $projectsCount . ' مشروع');
            }

            $city->delete();

            DB::commit();

            return redirect()->route('settings.cities.index')
                ->with('success', 'تم حذف المدينة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف المدينة: ' . $e->getMessage());
        }
    }
}
