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
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'expires_at' => 'date',
        'total_price' => 'decimal:2',
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
        
        $variables = [
            '{{client_name}}' => $this->client->name ?? 'غير محدد',
            '{{project_name}}' => $this->project->name ?? 'غير محدد',
            '{{service_name}}' => $this->service->name ?? 'غير محدد',
            '{{date}}' => $this->created_at->format('Y-m-d'),
            '{{total_price}}' => $this->total_price ? number_format($this->total_price, 2) . ' ر.س' : 'غير محدد',
        ];
        
        foreach ($variables as $key => $value) {
            $content = str_replace($key, $value, $content);
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
