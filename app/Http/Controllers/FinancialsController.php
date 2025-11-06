<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinancialsController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        return view('financials.index');
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        return view('financials.create');
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implementation
        return redirect()->route('financials.index')->with('success', 'تم إنشاء الفاتورة بنجاح');
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $id)
    {
        return view('financials.show', compact('id'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(string $id)
    {
        return view('financials.edit', compact('id'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implementation
        return redirect()->route('financials.show', $id)->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implementation
        return redirect()->route('financials.index')->with('success', 'تم حذف الفاتورة بنجاح');
    }

    /**
     * Generate PDF for invoice.
     */
    public function generatePdf(string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم توليد ملف PDF بنجاح');
    }

    /**
     * Store a payment for an invoice.
     */
    public function storePayment(Request $request, string $id)
    {
        // TODO: Implementation
        return back()->with('success', 'تم إضافة الدفعة بنجاح');
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, string $id, string $paymentId)
    {
        // TODO: Implementation
        return back()->with('success', 'تم تحديث حالة الدفعة بنجاح');
    }
}


