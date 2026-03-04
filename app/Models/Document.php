<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'type',
        'template_id',
        'project_id',
        'client_id',
        'service_id',
        'title',
        'content',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'total_price',
        'expires_at',
        'pdf_path',
        // حقول عروض الأسعار المنظمة
        'issue_date',
        'valid_until',
        'subtotal',
        'discount_type',
        'discount_value',
        'vat_percent',
        'vat_amount',
        'total_in_words',
        'terms_html',
        'notes_internal',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'expires_at' => 'date',
        'issue_date' => 'date',
        'valid_until' => 'date',
        'total_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
    ];

    /**
     * القالب المرتبط
     */
    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * المشروع المرتبط
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * العميل المرتبط
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * الخدمة المرتبطة
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * بنود عرض السعر (للعروض فقط)
     */
    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class, 'document_id')->orderBy('position');
    }

    /**
     * منشئ المستند
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * المعتمد للمستند
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * سجلات الاعتماد
     */
    public function approvals()
    {
        return $this->hasMany(DocumentApproval::class);
    }

    /**
     * التحقق من إمكانية التعديل
     */
    public function canBeEdited(): bool
    {
        // التقارير المعتمدة لا يمكن تعديلها
        if ($this->type === 'technical_report' && $this->status === 'approved') {
            return false;
        }
        
        return in_array($this->status, ['draft', 'rejected']);
    }

    /**
     * التحقق من إمكانية الحذف
     */
    public function canBeDeleted(): bool
    {
        // المستندات المعتمدة أو المرسلة لا يمكن حذفها
        return !in_array($this->status, ['approved', 'submitted', 'sent', 'accepted']);
    }

    /**
     * التحقق من انتهاء صلاحية العرض
     */
    public function isExpired(): bool
    {
        if ($this->type !== 'quotation' || !$this->expires_at) {
            return false;
        }
        
        return Carbon::now()->greaterThan($this->expires_at);
    }

    /**
     * توليد رقم مستند جديد
     */
    public static function generateDocumentNumber(string $type): string
    {
        $prefix = $type === 'technical_report' ? 'TR' : 'QT';
        $year = date('Y');
        $month = date('m');
        
        $lastDocument = self::where('type', $type)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastDocument ? (int) substr($lastDocument->document_number, -4) + 1 : 1;
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $number);
    }

    /**
     * استبدال المتغيرات في المحتوى
     */
    public function replaceVariables($content = null): string
    {
        $content = $content ?? $this->content;
        
        if (empty($content)) {
            return '';
        }
        
        // جلب القيم مع التأكد من تحميل العلاقات
        $clientName = 'غير محدد';
        if ($this->relationLoaded('client') && $this->client) {
            $clientName = $this->client->name;
        } elseif ($this->client_id) {
            $client = \App\Models\Client::find($this->client_id);
            $clientName = $client ? $client->name : 'غير محدد';
        }
        
        $projectName = 'غير محدد';
        if ($this->relationLoaded('project') && $this->project) {
            $projectName = $this->project->name;
        } elseif ($this->project_id) {
            $project = \App\Models\Project::find($this->project_id);
            $projectName = $project ? $project->name : 'غير محدد';
        }
        
        $serviceName = 'غير محدد';
        if ($this->relationLoaded('service') && $this->service) {
            $serviceName = $this->service->name;
        } elseif ($this->service_id) {
            $service = \App\Models\Service::find($this->service_id);
            $serviceName = $service ? $service->name : 'غير محدد';
        }
        
        $date = $this->created_at ? $this->created_at->format('Y-m-d') : date('Y-m-d');
        $totalPrice = $this->total_price ? number_format($this->total_price, 2) . ' ر.س' : 'غير محدد';
        
        $variables = [
            'client_name' => $clientName,
            'project_name' => $projectName,
            'service_name' => $serviceName,
            'date' => $date,
            'total_price' => $totalPrice,
        ];
        
        // استبدال المتغيرات بكل الأشكال الممكنة: {{variable}} و @{{variable}}
        foreach ($variables as $key => $value) {
            // استبدال @{{variable}} أولاً
            $content = str_replace('@{{' . $key . '}}', $value, $content);
            // ثم استبدال {{variable}}
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Scopes
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}
