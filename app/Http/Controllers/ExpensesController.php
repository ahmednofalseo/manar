<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index()
    {
        return view('expenses.index');
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implementation
        return redirect()->route('expenses.index')->with('success', 'تم إضافة المصروف بنجاح');
    }

    /**
     * Display the specified expense.
     */
    public function show(string $id)
    {
        return view('expenses.show', compact('id'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(string $id)
    {
        return view('expenses.edit', compact('id'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementation
        return redirect()->route('expenses.show', $id)->with('success', 'تم تحديث المصروف بنجاح');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implementation
        return redirect()->route('expenses.index')->with('success', 'تم حذف المصروف بنجاح');
    }

    /**
     * Approve an expense.
     */
    public function approve(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم اعتماد المصروف بنجاح');
    }

    /**
     * Reject an expense.
     */
    public function reject(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم رفض المصروف بنجاح');
    }
}


