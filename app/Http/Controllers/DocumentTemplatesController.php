<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class DocumentTemplatesController extends Controller
{
    /**
     * عرض قائمة القوالب
     */
    public function index()
    {
        Gate::authorize('viewAny', DocumentTemplate::class);

        $templates = DocumentTemplate::orderBy('type')->orderBy('order')->get();

        return view('document-templates.index', compact('templates'));
    }

    /**
     * عرض نموذج إنشاء قالب
     */
    public function create()
    {
        Gate::authorize('create', DocumentTemplate::class);

        return view('document-templates.create');
    }

    /**
     * حفظ قالب جديد
     */
    public function store(Request $request)
    {
        Gate::authorize('create', DocumentTemplate::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:technical_report,quotation',
            'content' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        DocumentTemplate::create($validated);

        return redirect()->route('document-templates.index')
            ->with('success', 'تم إنشاء القالب بنجاح');
    }

    /**
     * عرض قالب
     */
    public function show(DocumentTemplate $documentTemplate)
    {
        Gate::authorize('view', $documentTemplate);

        return view('document-templates.show', compact('documentTemplate'));
    }

    /**
     * عرض نموذج تعديل قالب
     */
    public function edit(DocumentTemplate $documentTemplate)
    {
        Gate::authorize('update', $documentTemplate);

        return view('document-templates.edit', compact('documentTemplate'));
    }

    /**
     * تحديث قالب
     */
    public function update(Request $request, DocumentTemplate $documentTemplate)
    {
        Gate::authorize('update', $documentTemplate);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:technical_report,quotation',
            'content' => 'nullable|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
            'order' => 'integer',
        ]);

        $documentTemplate->update($validated);

        return redirect()->route('document-templates.index')
            ->with('success', 'تم تحديث القالب بنجاح');
    }

    /**
     * حذف قالب
     */
    public function destroy(DocumentTemplate $documentTemplate)
    {
        Gate::authorize('delete', $documentTemplate);

        // التحقق من وجود مستندات مرتبطة
        if ($documentTemplate->documents()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف القالب لوجود مستندات مرتبطة به');
        }

        $documentTemplate->delete();

        return redirect()->route('document-templates.index')
            ->with('success', 'تم حذف القالب بنجاح');
    }
}
