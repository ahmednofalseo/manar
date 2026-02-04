<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\ClientAttachment;
use App\Models\ClientNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ClientsController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Client::class);

        $query = Client::withCount('projects')->latest();

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('national_id_or_cr', 'like', "%{$search}%");
            });
        }

        // فلترة حسب المدينة
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', session('clients_per_page', 15));
        session(['clients_per_page' => $perPage]);
        $clients = $query->paginate($perPage)->appends($request->query());

        // الإحصائيات
        $totalClients = Client::count();
        $activeClients = Client::where('status', 'active')->count();
        $inactiveClients = Client::where('status', 'inactive')->count();

        // المدن للفلترة
        $cities = Client::select('city')->distinct()->orderBy('city')->pluck('city');

        // بيانات العملاء الجدد شهريًا (آخر 12 شهر)
        $newClientsByMonth = Client::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // توزيع العملاء حسب النوع
        $clientsByType = Client::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // العملاء الأكثر نشاطًا (حسب عدد المشاريع)
        $mostActiveClients = Client::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'projects_count']);

        return view('clients.index', compact(
            'clients',
            'totalClients',
            'activeClients',
            'inactiveClients',
            'cities',
            'newClientsByMonth',
            'clientsByType',
            'mostActiveClients'
        ));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        Gate::authorize('create', Client::class);

        return view('clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(StoreClientRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // إنشاء العميل
            $client = Client::create($data);

            DB::commit();

            return redirect()->route('clients.show', $client->id)
                ->with('success', 'تم إنشاء العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء العميل: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified client.
     */
    public function show(string $id)
    {
        $client = Client::with(['projects', 'attachments.uploader', 'notes.creator'])
            ->withCount('projects')
            ->findOrFail($id);

        Gate::authorize('view', $client);

        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);

        Gate::authorize('update', $client);

        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(UpdateClientRequest $request, string $id)
    {
        $client = Client::findOrFail($id);

        DB::beginTransaction();
        try {
            $data = $request->validated();

            $client->update($data);

            DB::commit();

            return redirect()->route('clients.show', $client->id)
                ->with('success', 'تم تحديث بيانات العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث العميل: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);

        Gate::authorize('delete', $client);

        DB::beginTransaction();
        try {
            // حذف المرفقات
            foreach ($client->attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }

            // حذف العميل (soft delete)
            $client->delete();

            DB::commit();

            return redirect()->route('clients.index')
                ->with('success', 'تم حذف العميل بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف العميل: ' . $e->getMessage());
        }
    }

    /**
     * Store attachment for client.
     */
    public function storeAttachment(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        Gate::authorize('uploadAttachment', $client);

        $validated = $request->validate([
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'attachments.required' => 'الملفات مطلوبة',
            'attachments.array' => 'يجب أن تكون الملفات مصفوفة',
            'attachments.min' => 'يجب رفع ملف واحد على الأقل',
            'attachments.*.required' => 'الملف مطلوب',
            'attachments.*.file' => 'يجب أن يكون ملف صحيح',
            'attachments.*.mimes' => 'نوع الملف يجب أن يكون: pdf, jpg, jpeg, png',
            'attachments.*.max' => 'حجم الملف يجب أن يكون أقل من 5 ميجابايت',
        ]);

        try {
            $uploadedFiles = [];

            foreach ($request->file('attachments') as $file) {
                $directory = "clients/{$client->id}/attachments";
                $filePath = $file->store($directory, 'public');

                $attachment = ClientAttachment::create([
                    'client_id' => $client->id,
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);

                $uploadedFiles[] = $attachment;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم رفع ' . count($uploadedFiles) . ' ملف بنجاح',
                    'attachments' => $uploadedFiles,
                ]);
            }

            return back()->with('success', 'تم رفع ' . count($uploadedFiles) . ' ملف بنجاح');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء رفع الملفات: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'حدث خطأ أثناء رفع الملفات: ' . $e->getMessage());
        }
    }

    /**
     * Delete attachment.
     */
    public function destroyAttachment(Request $request, string $clientId, string $attachmentId)
    {
        $client = Client::findOrFail($clientId);
        $attachment = ClientAttachment::where('client_id', $client->id)
            ->findOrFail($attachmentId);

        Gate::authorize('uploadAttachment', $client);

        try {
            // حذف الملف من التخزين
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف المرفق بنجاح',
                ]);
            }

            return back()->with('success', 'تم حذف المرفق بنجاح');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage());
        }
    }

    /**
     * Store note for client.
     */
    public function storeNote(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        Gate::authorize('addNote', $client);

        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ], [
            'body.required' => 'نص الملاحظة مطلوب',
            'body.string' => 'نص الملاحظة يجب أن يكون نص',
            'body.max' => 'نص الملاحظة يجب ألا يتجاوز 5000 حرف',
        ]);

        try {
            $note = ClientNote::create([
                'client_id' => $client->id,
                'body' => $validated['body'],
                'created_by' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إضافة الملاحظة بنجاح',
                    'note' => $note->load('creator'),
                ]);
            }

            return back()->with('success', 'تم إضافة الملاحظة بنجاح');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إضافة الملاحظة: ' . $e->getMessage(),
                ], 422);
            }

            return back()->with('error', 'حدث خطأ أثناء إضافة الملاحظة: ' . $e->getMessage());
        }
    }

    /**
     * Export clients.
     */
    public function export(Request $request)
    {
        Gate::authorize('viewAny', Client::class);

        $query = Client::withCount('projects')->latest();

        // تطبيق نفس الفلاتر المستخدمة في index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('national_id_or_cr', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $clients = $query->get();

        $filename = 'clients_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8 Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'الاسم',
                'النوع',
                'الهوية/السجل التجاري',
                'الجوال',
                'البريد الإلكتروني',
                'المدينة',
                'الحي',
                'العنوان',
                'الحالة',
                'عدد المشاريع',
                'تاريخ الإنشاء',
            ]);

            // Data
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->name,
                    $client->type_label,
                    $client->national_id_or_cr ?? '',
                    $client->phone,
                    $client->email ?? '',
                    $client->city,
                    $client->district ?? '',
                    $client->address ?? '',
                    $client->status_label,
                    $client->projects_count,
                    $client->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import clients.
     */
    public function import(Request $request)
    {
        Gate::authorize('create', Client::class);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file.required' => 'الملف مطلوب',
            'file.file' => 'يجب أن يكون ملف',
            'file.mimes' => 'نوع الملف يجب أن يكون CSV',
            'file.max' => 'حجم الملف يجب أن يكون أقل من 5 ميجابايت',
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');
            
            // Skip BOM if present
            $firstLine = fgets($handle);
            if (substr($firstLine, 0, 3) !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }

            // Skip header row
            fgetcsv($handle);

            $imported = 0;
            $errors = [];

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 3) continue;

                try {
                    $data = [
                        'name' => $row[0] ?? '',
                        'type' => $this->mapTypeFromArabic($row[1] ?? 'individual'),
                        'national_id_or_cr' => $row[2] ?? null,
                        'phone' => $row[3] ?? '',
                        'email' => $row[4] ?? null,
                        'city' => $row[5] ?? '',
                        'district' => $row[6] ?? null,
                        'address' => $row[7] ?? null,
                        'status' => $this->mapStatusFromArabic($row[8] ?? 'active'),
                    ];

                    if (empty($data['name']) || empty($data['phone'])) {
                        continue;
                    }

                    Client::create($data);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = 'خطأ في السطر: ' . implode(', ', $row) . ' - ' . $e->getMessage();
                }
            }

            fclose($handle);

            DB::commit();

            $message = "تم استيراد {$imported} عميل بنجاح";
            if (!empty($errors)) {
                $message .= '. ' . count($errors) . ' أخطاء: ' . implode('; ', array_slice($errors, 0, 5));
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage());
        }
    }

    /**
     * Map Arabic type to English.
     */
    private function mapTypeFromArabic(string $type): string
    {
        return match(trim($type)) {
            'فرد' => 'individual',
            'شركة' => 'company',
            'جهة حكومية', 'حكومي' => 'government',
            default => 'individual',
        };
    }

    /**
     * Map Arabic status to English.
     */
    private function mapStatusFromArabic(string $status): string
    {
        return match(trim($status)) {
            'نشط', 'نشطة' => 'active',
            'غير نشط', 'غير نشطة' => 'inactive',
            default => 'active',
        };
    }

    /**
     * Bulk delete clients.
     */
    public function bulkDelete(Request $request)
    {
        Gate::authorize('deleteAny', Client::class);

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:clients,id',
        ], [
            'ids.required' => 'يجب اختيار عملاء للحذف',
            'ids.array' => 'يجب أن تكون المعرفات مصفوفة',
            'ids.min' => 'يجب اختيار عميل واحد على الأقل',
            'ids.*.exists' => 'أحد العملاء المختارين غير موجود',
        ]);

        DB::beginTransaction();
        try {
            $clients = Client::whereIn('id', $validated['ids'])->get();
            $deleted = 0;

            foreach ($clients as $client) {
                // حذف المرفقات
                foreach ($client->attachments as $attachment) {
                    if (Storage::disk('public')->exists($attachment->file_path)) {
                        Storage::disk('public')->delete($attachment->file_path);
                    }
                    $attachment->delete();
                }

                $client->delete();
                $deleted++;
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "تم حذف {$deleted} عميل بنجاح",
                ]);
            }

            return back()->with('success', "تم حذف {$deleted} عميل بنجاح");
        } catch (\Exception $e) {
            DB::rollBack();            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage(),
                ], 422);
            }            return back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }
}