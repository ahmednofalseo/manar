<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Models\Client;
use App\Models\Service;
use App\Models\QuotationItem;
use App\Helpers\NumberToWords;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Requests\ApproveDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;
use Mpdf\Mpdf;

class DocumentsController extends Controller
{
    /**
     * عرض قائمة المستندات
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Document::class);

        $type = $request->get('type', 'technical_report'); // default to technical_report
        $user = auth()->user();

        $query = Document::with(['project', 'client', 'service', 'creator', 'approver'])
            ->where('type', $type);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%");
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

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data for filters
        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        
        $statuses = $type === 'technical_report' 
            ? ['draft' => 'مسودة', 'submitted' => 'مرسل', 'approved' => 'معتمد', 'rejected' => 'مرفوض']
            : ['draft' => 'مسودة', 'sent' => 'مرسل', 'accepted' => 'مقبول', 'rejected' => 'مرفوض', 'expired' => 'منتهي'];

        return view('documents.index', compact('documents', 'type', 'projects', 'clients', 'statuses'));
    }

    /**
     * عرض نموذج إنشاء مستند
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Document::class);

        $type = $request->get('type', 'technical_report');
        $projectId = $request->get('project_id');
        $clientId = $request->get('client_id');
        
        // Convert to integer if exists
        if ($clientId) {
            $clientId = (int) $clientId;
        }
        if ($projectId) {
            $projectId = (int) $projectId;
        }

        $templates = DocumentTemplate::active()->ofType($type)->orderBy('order')->get();
        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        // Auto-fill client from project
        $project = null;
        if ($projectId) {
            $project = Project::find($projectId);
            if ($project && $project->client_id) {
                $clientId = $project->client_id;
            }
        }

        // لعروض الأسعار، استخدم view منفصل
        if ($type === 'quotation') {
            // جلب بيانات المكتب من الإعدادات
            $officeName = \App\Models\Setting::get('system_name', 'مكتب المنار للاستشارات الهندسية');
            $officeLicense = \App\Models\Setting::get('office_license', '');
            $officeAddress = \App\Models\Setting::get('office_address', '');
            $officePhone = \App\Models\Setting::get('office_phone', '');
            $officeEmail = \App\Models\Setting::get('office_email', '');
            
            // نص الشروط الافتراضي
            $defaultTerms = '<p>1. هذا العرض ساري لمدة 30 يوم من تاريخه.</p>
<p>2. السعر يشمل جميع الضرائب والرسوم المقررة.</p>
<p>3. يتم الدفع حسب الاتفاق المذكور في العقد.</p>
<p>4. جميع الأعمال تتم وفقاً للمواصفات والمخططات المعتمدة.</p>
<p>5. يتحمل العميل أي تكاليف إضافية ناتجة عن تعديلات غير مذكورة في العرض.</p>';
            
            return view('documents.quotations.create', compact(
                'type', 
                'projects', 
                'clients', 
                'services', 
                'projectId', 
                'clientId', 
                'project',
                'officeName',
                'officeLicense',
                'officeAddress',
                'officePhone',
                'officeEmail',
                'defaultTerms'
            ));
        }
        
        return view('documents.create', compact('type', 'templates', 'projects', 'clients', 'services', 'projectId', 'clientId', 'project'));
    }

    /**
     * حفظ مستند جديد
     */
    public function store(StoreDocumentRequest $request)
    {
        Gate::authorize('create', Document::class);

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['document_number'] = Document::generateDocumentNumber($data['type']);
            $data['created_by'] = auth()->id();
            $data['status'] = $data['status'] ?? 'draft';
            
            // Auto-generate title for quotations if not provided
            if ($data['type'] === 'quotation' && empty($data['title'])) {
                $client = Client::find($data['client_id']);
                $data['title'] = 'عرض سعر - ' . ($client ? $client->name : 'عميل');
            }

            // Auto-fill client from project if not provided
            if (!$data['client_id'] && $data['project_id']) {
                $project = Project::find($data['project_id']);
                if ($project && $project->client_id) {
                    $data['client_id'] = $project->client_id;
                }
            }

            // Handle quotation items if type is quotation
            $items = [];
            if ($data['type'] === 'quotation') {
                // Check if items exist in request
                if ($request->has('items') && $request->filled('items')) {
                    $itemsJson = $request->input('items');
                    if (is_string($itemsJson)) {
                        $items = json_decode($itemsJson, true) ?: [];
                    } else {
                        $items = $itemsJson ?: [];
                    }
                }
                
                // Log for debugging
                \Log::info('Quotation items received', [
                    'items_count' => count($items),
                    'items_json' => $request->input('items'),
                    'items_decoded' => $items
                ]);
                
                // Calculate totals
                $subtotal = 0;
                foreach ($items as $item) {
                    $lineTotal = (float)($item['qty'] ?? 0) * (float)($item['unit_price'] ?? 0);
                    $subtotal += $lineTotal;
                }
                
                $data['subtotal'] = $subtotal;
                
                // Calculate discount
                $discount = 0;
                if ($request->filled('discount_type') && $request->filled('discount_value')) {
                    $discountType = $request->input('discount_type');
                    $discountValue = (float)$request->input('discount_value');
                    if ($discountType === 'amount') {
                        $discount = $discountValue;
                    } elseif ($discountType === 'percent') {
                        $discount = ($subtotal * $discountValue) / 100;
                    }
                }
                $data['discount_type'] = $request->input('discount_type');
                $data['discount_value'] = $request->input('discount_value');
                
                // Calculate VAT
                $vatPercent = (float)($request->input('vat_percent') ?? 0);
                $afterDiscount = $subtotal - $discount;
                $vatAmount = ($afterDiscount * $vatPercent) / 100;
                $data['vat_percent'] = $vatPercent;
                $data['vat_amount'] = $vatAmount;
                
                // Calculate total
                $total = $afterDiscount + $vatAmount;
                $data['total_price'] = $total;
                
                // Convert total to words
                $data['total_in_words'] = NumberToWords::convert($total);
                
                // Set content to null for quotations as it's structured
                $data['content'] = null;
            } else {
                // For technical reports, ensure content is present
                if (!isset($data['content'])) {
                    $data['content'] = '';
                }
            }

            $document = Document::create($data);

            // Save quotation items - only save valid items with names
            if ($data['type'] === 'quotation') {
                $savedItemsCount = 0;
                if (!empty($items)) {
                    foreach ($items as $index => $item) {
                        // Skip empty items
                        if (empty($item['item_name']) || trim($item['item_name']) === '') {
                            continue;
                        }
                        
                        $qty = (float)($item['qty'] ?? 1);
                        $unitPrice = (float)($item['unit_price'] ?? 0);
                        $lineTotal = $qty * $unitPrice;
                        
                        QuotationItem::create([
                            'document_id' => $document->id,
                            'item_name' => trim($item['item_name']),
                            'description' => trim($item['description'] ?? ''),
                            'qty' => $qty,
                            'unit' => $item['unit'] ?? 'قطعة',
                            'unit_price' => $unitPrice,
                            'line_total' => round($lineTotal, 2),
                            'position' => $item['position'] ?? $index,
                        ]);
                        $savedItemsCount++;
                    }
                }
                
                \Log::info('Quotation items saved', [
                    'document_id' => $document->id,
                    'items_received' => count($items),
                    'items_saved' => $savedItemsCount
                ]);
            }

            DB::commit();

            // Redirect based on type
            if ($data['type'] === 'quotation') {
                return redirect()->route('documents.show', $document)
                    ->with('success', 'تم إنشاء عرض السعر بنجاح');
            }
            
            return redirect()->route('documents.edit', $document)
                ->with('success', 'تم إنشاء المستند بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            // Return JSON if AJAX request
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى التحقق من البيانات المدخلة',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()->withInput()
                ->withErrors($e->errors())
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating document', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            // Return JSON if AJAX request
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إنشاء المستند: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 422);
            }
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المستند: ' . $e->getMessage());
        }
    }

    /**
     * عرض مستند
     */
    public function show(Document $document)
    {
        Gate::authorize('view', $document);

        $document->load(['project', 'client', 'service', 'template', 'creator', 'approver', 'approvals.approver']);

        return view('documents.show', compact('document'));
    }

    /**
     * عرض نموذج تعديل مستند
     */
    public function edit(Document $document)
    {
        Gate::authorize('update', $document);

        if (!$document->canBeEdited()) {
            return redirect()->route('documents.show', $document)
                ->with('error', 'لا يمكن تعديل هذا المستند');
        }

        $document->load(['project', 'client', 'service', 'template']);

        $templates = DocumentTemplate::active()->ofType($document->type)->orderBy('order')->get();
        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('documents.edit', compact('document', 'templates', 'projects', 'clients', 'services'));
    }

    /**
     * تحديث مستند
     */
    public function update(UpdateDocumentRequest $request, Document $document)
    {
        Gate::authorize('update', $document);

        if (!$document->canBeEdited()) {
            return back()->with('error', 'لا يمكن تعديل هذا المستند');
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Auto-fill client from project if not provided
            if (!$data['client_id'] && $data['project_id']) {
                $project = Project::find($data['project_id']);
                if ($project && $project->client_id) {
                    $data['client_id'] = $project->client_id;
                }
            }

            $document->update($data);

            DB::commit();

            return redirect()->route('documents.show', $document)
                ->with('success', 'تم تحديث المستند بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المستند: ' . $e->getMessage());
        }
    }

    /**
     * حذف مستند
     */
    public function destroy(Document $document)
    {
        Gate::authorize('delete', $document);

        if (!$document->canBeDeleted()) {
            return back()->with('error', 'لا يمكن حذف هذا المستند');
        }

        DB::beginTransaction();
        try {
            // حذف ملف PDF إن وجد
            if ($document->pdf_path && Storage::disk('public')->exists($document->pdf_path)) {
                Storage::disk('public')->delete($document->pdf_path);
            }

            $document->delete();

            DB::commit();

            return redirect()->route('documents.index', ['type' => $document->type])
                ->with('success', 'تم حذف المستند بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف المستند: ' . $e->getMessage());
        }
    }

    /**
     * نسخ مستند
     */
    public function duplicate(Document $document)
    {
        Gate::authorize('duplicate', $document);

        DB::beginTransaction();
        try {
            $newDocument = $document->replicate();
            $newDocument->document_number = Document::generateDocumentNumber($document->type);
            $newDocument->status = 'draft';
            $newDocument->created_by = auth()->id();
            $newDocument->approved_by = null;
            $newDocument->approved_at = null;
            $newDocument->rejection_reason = null;
            $newDocument->pdf_path = null;
            $newDocument->save();

            DB::commit();

            return redirect()->route('documents.edit', $newDocument)
                ->with('success', 'تم إنشاء نسخة جديدة من المستند بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء نسخ المستند: ' . $e->getMessage());
        }
    }

    /**
     * إرسال مستند للاعتماد (للتقارير) أو للعميل (لعروض الأسعار)
     */
    public function submit(Document $document)
    {
        Gate::authorize('submit', $document);

        if ($document->status !== 'draft') {
            return back()->with('error', 'يمكن إرسال المستندات في حالة مسودة فقط');
        }

        DB::beginTransaction();
        try {
            if ($document->type === 'technical_report') {
                $document->update(['status' => 'submitted']);
                $message = 'تم إرسال التقرير للاعتماد بنجاح';
            } else {
                $document->update(['status' => 'sent']);
                $message = 'تم إرسال عرض السعر للعميل بنجاح';
            }

            DB::commit();

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إرسال المستند: ' . $e->getMessage());
        }
    }

    /**
     * اعتماد أو رفض مستند
     */
    public function approve(ApproveDocumentRequest $request, Document $document)
    {
        Gate::authorize('approve', $document);

        if ($document->type !== 'technical_report') {
            return back()->with('error', 'يمكن اعتماد التقارير الفنية فقط');
        }

        if ($document->status !== 'submitted') {
            return back()->with('error', 'يمكن اعتماد التقارير المرسلة فقط');
        }

        DB::beginTransaction();
        try {
            $action = $request->input('action');
            $reason = $request->input('reason');

            if ($action === 'approved') {
                $document->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);

                // توليد PDF نهائي
                $this->generatePdf($document);

                // تسجيل الاعتماد
                $document->approvals()->create([
                    'approved_by' => auth()->id(),
                    'action' => 'approved',
                    'reason' => $reason,
                ]);

                $message = 'تم اعتماد التقرير بنجاح';
            } else {
                $document->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                ]);

                // تسجيل الرفض
                $document->approvals()->create([
                    'approved_by' => auth()->id(),
                    'action' => 'rejected',
                    'reason' => $reason,
                ]);

                $message = 'تم رفض التقرير';
            }

            DB::commit();

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب: ' . $e->getMessage());
        }
    }

    /**
     * توليد PDF للمستند
     */
    public function generatePdf(Document $document)
    {
        Gate::authorize('view', $document);

        // للتقارير المعتمدة فقط
        if ($document->type === 'technical_report' && $document->status !== 'approved') {
            return redirect()->route('documents.show', $document)
                ->with('error', 'يمكن تحميل PDF للتقارير المعتمدة فقط');
        }

        $document->load(['project', 'client', 'service', 'creator', 'approver', 'quotationItems']);

        try {
            // لعروض الأسعار، استخدم mPDF مع دعم العربية
            if ($document->type === 'quotation') {
                return $this->generateQuotationPdf($document);
            }

            // للتقارير الفنية، استخدم Dompdf
            $html = view('documents.pdf.document', compact('document'))->render();

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('chroot', public_path());

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // حفظ PDF (فقط للتقارير المعتمدة)
            if ($document->type === 'technical_report' && $document->status === 'approved') {
                $filename = 'documents/' . $document->document_number . '.pdf';
                Storage::disk('public')->put($filename, $dompdf->output());
                $document->update(['pdf_path' => $filename]);
            }

            return $dompdf->stream("{$document->document_number}.pdf", ['Attachment' => false]);
        } catch (\Exception $e) {
            return redirect()->route('documents.show', $document)
                ->with('error', 'حدث خطأ أثناء توليد PDF: ' . $e->getMessage());
        }
    }

    /**
     * توليد PDF لعرض السعر باستخدام mPDF
     */
    private function generateQuotationPdf(Document $document)
    {
        $html = view('documents.pdf.quotation', compact('document'))->render();

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
        
        // حفظ PDF
        $filename = 'documents/' . $document->document_number . '.pdf';
        Storage::disk('public')->put($filename, $mpdf->Output('', 'S'));
        $document->update(['pdf_path' => $filename]);

        return $mpdf->Output("{$document->document_number}.pdf", 'I');
    }

    /**
     * معاينة PDF
     */
    public function previewPdf(Document $document)
    {
        Gate::authorize('view', $document);

        $document->load(['project', 'client', 'service', 'creator', 'approver', 'quotationItems']);

        try {
            // لعروض الأسعار، استخدم mPDF
            if ($document->type === 'quotation') {
                return $this->generateQuotationPdf($document);
            }

            // للتقارير الفنية، استخدم Dompdf
            $html = view('documents.pdf.document', compact('document'))->render();

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('chroot', public_path());

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            return $dompdf->stream("preview-{$document->document_number}.pdf", ['Attachment' => false]);
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء معاينة PDF: ' . $e->getMessage());
        }
    }
}
