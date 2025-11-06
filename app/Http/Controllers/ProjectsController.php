<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('projects.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implementation
        return redirect()->route('projects.index')->with('success', 'تم إنشاء المشروع بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('projects.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('projects.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementation
        return redirect()->route('projects.show', $id)->with('success', 'تم تحديث المشروع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implementation
        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح');
    }

    // Sub methods
    public function storeAttachment(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم رفع المرفق بنجاح');
    }

    public function updateStage(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم تحديث المرحلة بنجاح');
    }

    public function tasksIndex(string $id)
    {
        // TODO: Implementation
        return view('projects.show', compact('id'));
    }

    public function financialsIndex(string $id)
    {
        // TODO: Implementation
        return view('projects.show', compact('id'));
    }

    public function storeInvoice(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم إنشاء الفاتورة بنجاح');
    }

    public function storeThirdParty(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم إضافة الطرف الثالث بنجاح');
    }
}
