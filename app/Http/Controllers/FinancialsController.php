<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Events\PaymentCreated;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\UpdatePaymentStatusRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Helpers\PermissionHelper;
use Mpdf\Mpdf;

class FinancialsController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.view') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        Gate::authorize('viewAny', Invoice::class);

        $query = Invoice::with(['project', 'client', 'payments']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        $invoices = $query->latest('issue_date')->paginate(15);

        // KPIs
        $totalInvoicesThisMonth = Invoice::whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year)
            ->count();

        $totalCollected = Payment::where('status', \App\Enums\PaymentStatus::PAID)
            ->sum('amount');

        $totalRemaining = Invoice::sum(DB::raw('total_amount - paid_amount'));

        $collectionRate = Invoice::sum('total_amount') > 0
            ? round((Invoice::sum('paid_amount') / Invoice::sum('total_amount')) * 100, 2)
            : 0;

        // For filters
        $projects = Project::orderBy('name')->get();
        $clients = Client::where('status', 'active')->orderBy('name')->get();

        return view('financials.index', compact(
            'invoices',
            'totalInvoicesThisMonth',
            'totalCollected',
            'totalRemaining',
            'collectionRate',
            'projects',
            'clients'
        ));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.create') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        Gate::authorize('create', Invoice::class);

        $projects = Project::orderBy('name')->get();
        $clients = Client::where('status', 'active')->orderBy('name')->get();

        return view('financials.create', compact('projects', 'clients'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.create') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        Gate::authorize('create', Invoice::class);

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'project_id' => $request->project_id,
                'client_id' => $request->client_id,
                'number' => $request->number ?? Invoice::generateInvoiceNumber(),
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'total_amount' => $request->total_amount,
                'paid_amount' => 0,
                'status' => InvoiceStatus::UNPAID,
                'payment_method' => $request->payment_method ? PaymentMethod::from($request->payment_method) : null,
                'notes' => $request->notes,
            ]);

            // إنشاء Payments placeholders إذا كان هناك عدد دفعات
            if ($request->filled('installments_count') && $request->installments_count > 1) {
                $installmentAmount = $request->total_amount / $request->installments_count;
                $issueDate = \Carbon\Carbon::parse($request->issue_date);
                $dueDate = \Carbon\Carbon::parse($request->due_date);
                $daysBetween = $issueDate->diffInDays($dueDate) / $request->installments_count;

                for ($i = 1; $i <= $request->installments_count; $i++) {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'payment_no' => Payment::generatePaymentNumber(),
                        'amount' => $i === $request->installments_count 
                            ? $request->total_amount - ($installmentAmount * ($request->installments_count - 1))
                            : $installmentAmount,
                        'paid_at' => $issueDate->copy()->addDays(round($daysBetween * $i)),
                        'status' => \App\Enums\PaymentStatus::PENDING,
                        'method' => $request->payment_method ? PaymentMethod::from($request->payment_method) : PaymentMethod::TRANSFER,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('financials.show', $invoice->id)
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $id)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.view') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        $invoice = Invoice::with(['project', 'client', 'payments.creator'])->findOrFail($id);

        Gate::authorize('view', $invoice);

        return view('financials.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(string $id)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.edit') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        $invoice = Invoice::findOrFail($id);

        Gate::authorize('update', $invoice);

        $projects = Project::orderBy('name')->get();
        $clients = Client::where('status', 'active')->orderBy('name')->get();

        return view('financials.edit', compact('invoice', 'projects', 'clients'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(UpdateInvoiceRequest $request, string $id)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.edit') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        $invoice = Invoice::findOrFail($id);

        Gate::authorize('update', $invoice);

        DB::beginTransaction();
        try {
            $invoice->update([
                'project_id' => $request->project_id,
                'client_id' => $request->client_id,
                'number' => $request->number ?? $invoice->number,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method ? PaymentMethod::from($request->payment_method) : null,
                'notes' => $request->notes,
            ]);

            // تحديث الحالة بناءً على المبلغ المدفوع
            $invoice->updatePaidAmountAndStatus();

            DB::commit();

            return redirect()->route('financials.show', $invoice->id)
                ->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(string $id)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.delete') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        $invoice = Invoice::findOrFail($id);

        Gate::authorize('delete', $invoice);

        DB::beginTransaction();
        try {
            // حذف المرفقات
            foreach ($invoice->payments as $payment) {
                if ($payment->attachment) {
                    Storage::disk('public')->delete($payment->attachment);
                }
            }

            $invoice->delete();

            DB::commit();

            return redirect()->route('financials.index')
                ->with('success', 'تم حذف الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for invoice.
     */
    public function generatePdf(string $id)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.view') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        $invoice = Invoice::with(['project', 'client', 'payments'])->findOrFail($id);

        Gate::authorize('view', $invoice);

        return $this->generateInvoicePdf($invoice);
    }

    /**
     * Generate invoice PDF using mPDF
     */
    private function generateInvoicePdf(Invoice $invoice)
    {
        // التأكد من تحميل جميع البيانات المطلوبة
        $invoice->loadMissing(['project', 'client', 'payments']);
        
        $html = view('financials.pdf.invoice', compact('invoice'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_header' => 10,
            'margin_footer' => 10,
            'direction' => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans', // DejaVu Sans supports Arabic
        ]);

        $mpdf->WriteHTML($html);
        
        return $mpdf->Output("{$invoice->number}.pdf", 'I');
    }

    /**
     * Store a payment for an invoice.
     */
    public function storePayment(StorePaymentRequest $request, string $id)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.view') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        $invoice = Invoice::findOrFail($id);

        Gate::authorize('view', $invoice);

        DB::beginTransaction();
        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentPath = $file->store('payments', 'public');
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_no' => $request->payment_no ?? Payment::generatePaymentNumber(),
                'amount' => $request->amount,
                'paid_at' => $request->paid_at,
                'status' => \App\Enums\PaymentStatus::from($request->status),
                'method' => PaymentMethod::from($request->method),
                'notes' => $request->notes,
                'attachment' => $attachmentPath,
                'created_by' => auth()->id(),
            ]);

            // إطلاق Event لتحديث Invoice
            event(new PaymentCreated($payment));

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'تم إضافة الدفعة بنجاح']);
            }
            return back()->with('success', 'تم إضافة الدفعة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء إضافة الدفعة: ' . $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'حدث خطأ أثناء إضافة الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(UpdatePaymentStatusRequest $request, string $id, string $paymentId)
    {
        // التحقق من الصلاحية
        if (!PermissionHelper::hasPermission('financials.view') && !PermissionHelper::hasPermission('financials.manage')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        $invoice = Invoice::findOrFail($id);
        $payment = Payment::where('invoice_id', $invoice->id)->findOrFail($paymentId);

        Gate::authorize('view', $invoice);

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => \App\Enums\PaymentStatus::from($request->status),
            ]);

            // تحديث Invoice
            $invoice->updatePaidAmountAndStatus();

            DB::commit();

            return back()->with('success', 'تم تحديث حالة الدفعة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث حالة الدفعة: ' . $e->getMessage());
        }
    }
}
