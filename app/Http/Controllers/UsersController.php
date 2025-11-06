<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implementation
        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء الموظف بنجاح');
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        return view('admin.users.show', compact('id'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $id)
    {
        return view('admin.users.edit', compact('id'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementation
        return redirect()->route('admin.users.show', $id)->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implementation
        return redirect()->route('admin.users.index')->with('success', 'تم حذف الموظف بنجاح');
    }

    /**
     * Toggle user status (active/suspended).
     */
    public function toggle(string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم تحديث حالة الموظف بنجاح');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم إعادة ضبط كلمة المرور بنجاح');
    }

    /**
     * Update user roles and permissions.
     */
    public function updateRoles(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم تحديث الأدوار والصلاحيات بنجاح');
    }

    /**
     * Export users.
     */
    public function export(Request $request)
    {
        // TODO: Implementation
        return back()->with('success', 'تم تصدير البيانات بنجاح');
    }

    /**
     * Import users from CSV.
     */
    public function import(Request $request)
    {
        // TODO: Implementation
        return back()->with('success', 'تم استيراد البيانات بنجاح');
    }
}
