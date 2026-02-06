<?php

namespace App\Http\Controllers;

use App\Enums\ExpenseStatus;
use App\Http\Requests\ApproveExpenseRequest;
use App\Http\Requests\RejectExpenseRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ExpensesController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Expense::class);

        $query = Expense::with(['creator', 'approver', 'rejector', 'attachments']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('voucher_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->latest('date')->paginate(15);

        // KPIs
        $totalExpensesThisMonth = Expense::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $approvedExpenses = Expense::where('status', ExpenseStatus::APPROVED)
            ->sum('amount');

        $rejectedExpenses = Expense::where('status', ExpenseStatus::REJECTED)
            ->sum('amount');

        $pendingExpenses = Expense::where('status', ExpenseStatus::PENDING)
            ->sum('amount');

        // Charts Data
        $monthlyExpenses = Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->whereYear('date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $expensesByType = Expense::selectRaw('type, SUM(amount) as total, COUNT(*) as count')
            ->where('status', ExpenseStatus::APPROVED)
            ->groupBy('type')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Departments list for filter
        $departments = Expense::distinct()->pluck('department')->filter()->sort()->values();
        $types = Expense::distinct()->pluck('type')->filter()->sort()->values();

        return view('expenses.index', compact(
            'expenses',
            'totalExpensesThisMonth',
            'approvedExpenses',
            'rejectedExpenses',
            'pendingExpenses',
            'monthlyExpenses',
            'expensesByType',
            'departments',
            'types'
        ));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        Gate::authorize('create', Expense::class);

        return view('expenses.create');
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        Gate::authorize('create', Expense::class);

        DB::beginTransaction();
        try {
            $expense = Expense::create([
                'voucher_number' => $request->voucher_number ?? Expense::generateVoucherNumber(),
                'date' => $request->date,
                'department' => $request->department,
                'type' => $request->type,
                'description' => $request->description,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => $request->status ?? ExpenseStatus::PENDING,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // رفع المرفقات
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filePath = $file->store('expenses/' . $expense->id, 'public');
                    
                    ExpenseAttachment::create([
                        'expense_id' => $expense->id,
                        'name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('expenses.show', $expense->id)
                ->with('success', 'تم إضافة المصروف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إضافة المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified expense.
     */
    public function show(string $id)
    {
        $expense = Expense::with(['creator', 'approver', 'rejector', 'attachments.uploader'])->findOrFail($id);

        Gate::authorize('view', $expense);

        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(string $id)
    {
        $expense = Expense::with('attachments')->findOrFail($id);

        Gate::authorize('update', $expense);

        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(UpdateExpenseRequest $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        Gate::authorize('update', $expense);

        DB::beginTransaction();
        try {
            $expense->update([
                'voucher_number' => $request->voucher_number ?? $expense->voucher_number,
                'date' => $request->date,
                'department' => $request->department,
                'type' => $request->type,
                'description' => $request->description,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'status' => $request->status ?? $expense->status,
                'notes' => $request->notes,
            ]);

            // رفع مرفقات جديدة
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filePath = $file->store('expenses/' . $expense->id, 'public');
                    
                    ExpenseAttachment::create([
                        'expense_id' => $expense->id,
                        'name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('expenses.show', $expense->id)
                ->with('success', 'تم تحديث المصروف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::with('attachments')->findOrFail($id);

        Gate::authorize('delete', $expense);

        DB::beginTransaction();
        try {
            // حذف المرفقات من التخزين
            foreach ($expense->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $expense->delete();

            DB::commit();

            return redirect()->route('expenses.index')
                ->with('success', 'تم حذف المصروف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Approve an expense.
     */
    public function approve(ApproveExpenseRequest $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        Gate::authorize('approve', $expense);

        DB::beginTransaction();
        try {
            $expense->update([
                'status' => ExpenseStatus::APPROVED,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes' => $request->notes ? ($expense->notes . "\n\n[اعتماد]: " . $request->notes) : $expense->notes,
            ]);

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم اعتماد المصروف بنجاح']);
            }

            return back()->with('success', 'تم اعتماد المصروف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 422);
            }
            return back()->with('error', 'حدث خطأ أثناء اعتماد المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Reject an expense.
     */
    public function reject(RejectExpenseRequest $request, string $id)
    {
        $expense = Expense::findOrFail($id);

        Gate::authorize('reject', $expense);

        DB::beginTransaction();
        try {
            $expense->update([
                'status' => ExpenseStatus::REJECTED,
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_reason' => $request->notes,
                'notes' => $expense->notes . "\n\n[رفض]: " . $request->notes,
            ]);

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم رفض المصروف بنجاح']);
            }

            return back()->with('success', 'تم رفض المصروف بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 422);
            }
            return back()->with('error', 'حدث خطأ أثناء رفض المصروف: ' . $e->getMessage());
        }
    }

    /**
     * Download expense attachment.
     */
    public function downloadAttachment(string $id, string $attachmentId)
    {
        $expense = Expense::findOrFail($id);
        $attachment = ExpenseAttachment::where('expense_id', $expense->id)->findOrFail($attachmentId);

        Gate::authorize('view', $expense);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            return back()->with('error', 'الملف غير موجود');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->name);
    }

    /**
     * Delete expense attachment.
     */
    public function deleteAttachment(string $id, string $attachmentId)
    {
        $expense = Expense::findOrFail($id);
        $attachment = ExpenseAttachment::where('expense_id', $expense->id)->findOrFail($attachmentId);

        Gate::authorize('update', $expense);        DB::beginTransaction();
        try {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();            DB::commit();            return back()->with('success', 'تم حذف المرفق بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف المرفق: ' . $e->getMessage());
        }
    }
}
