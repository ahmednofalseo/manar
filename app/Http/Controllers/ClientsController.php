<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index()
    {
        return view('clients.index');
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implementation
        return redirect()->route('clients.index')->with('success', 'تم إنشاء العميل بنجاح');
    }

    /**
     * Display the specified client.
     */
    public function show(string $id)
    {
        return view('clients.show', compact('id'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(string $id)
    {
        return view('clients.edit', compact('id'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementation
        return redirect()->route('clients.show', $id)->with('success', 'تم تحديث بيانات العميل بنجاح');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implementation
        return redirect()->route('clients.index')->with('success', 'تم حذف العميل بنجاح');
    }

    /**
     * Store attachment for client.
     */
    public function storeAttachment(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم رفع المرفق بنجاح');
    }

    /**
     * Store note for client.
     */
    public function storeNote(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم إضافة الملاحظة بنجاح');
    }

    /**
     * Export clients.
     */
    public function export(Request $request)
    {
        // TODO: Implementation
        return back()->with('success', 'تم تصدير البيانات بنجاح');
    }

    /**
     * Import clients.
     */
    public function import(Request $request)
    {
        // TODO: Implementation
        return back()->with('success', 'تم استيراد البيانات بنجاح');
    }
}


