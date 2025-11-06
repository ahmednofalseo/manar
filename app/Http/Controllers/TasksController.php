<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TasksController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index()
    {
        return view('tasks.index');
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implementation
        return redirect()->route('tasks.index')->with('success', 'تم إنشاء المهمة بنجاح');
    }

    /**
     * Display the specified task.
     */
    public function show(string $id)
    {
        return view('tasks.show', compact('id'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(string $id)
    {
        return view('tasks.edit', compact('id'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementation
        return redirect()->route('tasks.show', $id)->with('success', 'تم تحديث المهمة بنجاح');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implementation
        return redirect()->route('tasks.index')->with('success', 'تم حذف المهمة بنجاح');
    }

    /**
     * Change task status.
     */
    public function changeStatus(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم تحديث حالة المهمة بنجاح');
    }

    /**
     * Add comment to task.
     */
    public function comment(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم إضافة الملاحظة بنجاح');
    }
}


