<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ServicesController extends Controller
{
    /**
     * عرض قائمة الخدمات
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Service::class);

        $query = Service::with(['category', 'parent', 'subServices'])->latest();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الفئة
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // فلترة الخدمات الرئيسية فقط
        if ($request->filled('main_only')) {
            $query->whereNull('parent_id');
        }

        $services = $query->paginate(20);
        $categories = ServiceCategory::where('is_active', true)->orderBy('order')->get();

        return view('services.index', compact('services', 'categories'));
    }

    /**
     * عرض نموذج إنشاء خدمة
     */
    public function create()
    {
        Gate::authorize('create', Service::class);

        $categories = ServiceCategory::where('is_active', true)->orderBy('order')->get();
        $parentServices = Service::where('has_sub_services', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('services.create', compact('categories', 'parentServices'));
    }

    /**
     * حفظ خدمة جديدة
     */
    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        
        // توليد slug تلقائياً إذا لم يُحدد
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $service = Service::create($data);

        return redirect()->route('services.index')
            ->with('success', 'تم إنشاء الخدمة بنجاح');
    }

    /**
     * عرض تفاصيل خدمة
     */
    public function show(Service $service)
    {
        Gate::authorize('view', $service);

        $service->load(['category', 'parent', 'subServices', 'workflowTemplates.steps']);

        return view('services.show', compact('service'));
    }

    /**
     * عرض نموذج تعديل خدمة
     */
    public function edit(Service $service)
    {
        Gate::authorize('update', $service);

        $categories = ServiceCategory::where('is_active', true)->orderBy('order')->get();
        $parentServices = Service::where('has_sub_services', true)
            ->where('is_active', true)
            ->where('id', '!=', $service->id)
            ->orderBy('name')
            ->get();

        return view('services.edit', compact('service', 'categories', 'parentServices'));
    }

    /**
     * تحديث خدمة
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        
        // توليد slug تلقائياً إذا لم يُحدد
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $service->update($data);

        return redirect()->route('services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * حذف خدمة
     */
    public function destroy(Service $service)
    {
        Gate::authorize('delete', $service);

        // التحقق من وجود مشاريع مرتبطة
        if ($service->projects()->count() > 0) {
            return redirect()->route('services.index')
                ->with('error', 'لا يمكن حذف الخدمة لوجود مشاريع مرتبطة بها');
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}
