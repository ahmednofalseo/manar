<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Project;
use App\Models\Client;
use App\Models\Service;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Requests\ApproveDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

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
            $data['status'] = 'draft';

            // Auto-fill client from project if not provided
            if (!$data['client_id'] && $data['project_id']) {
                $project = Project::find($data['project_id']);
                if ($project && $project->client_id) {
                    $data['client_id'] = $project->client_id;
                }
            }

            $document = Document::create($data);

            DB::commit();

            return redirect()->route('documents.edit', $document)
                ->with('success', 'تم إنشاء المستند بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
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

        $document->load(['project', 'client', 'service', 'creator', 'approver']);

        try {
            // توليد PDF
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
     * معاينة PDF
     */
    public function previewPdf(Document $document)
    {
        Gate::authorize('view', $document);

        $document->load(['project', 'client', 'service', 'creator', 'approver']);

        try {
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
