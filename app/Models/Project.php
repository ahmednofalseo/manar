<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'project_number',
        'type',
        'city',
        'district',
        'owner',
        'client_id',
        'value',
        'installments_count',
        'contract_number',
        'contract_file',
        'land_number',
        'land_code',
        'plan_file',
        'baladi_request_number',
        'stages',
        'status',
        'progress',
        'current_stage',
        'project_manager_id',
        'team_members',
        'internal_notes',
        'start_date',
        'end_date',
        'is_hidden',
    ];

    protected $casts = [
        'stages' => 'array',
        'team_members' => 'array',
        'value' => 'decimal:2',
        'progress' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_hidden' => 'boolean',
    ];

    /**
     * العميل
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * مدير المشروع
     */
    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    /**
     * أعضاء الفريق
     */
    public function teamUsers()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }

    /**
     * مراحل المشروع
     */
    public function projectStages()
    {
        return $this->hasMany(ProjectStage::class);
    }

    /**
     * مرفقات المشروع
     */
    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    /**
     * الأطراف الثالثة
     */
    public function thirdParties()
    {
        return $this->hasMany(ProjectThirdParty::class);
    }

    /**
     * مهام المشروع
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * الموافقات
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * الفواتير
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * توليد رقم مشروع تلقائي
     */
    public static function generateProjectNumber(): string
    {
        $year = date('Y');
        $lastProject = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastProject ? ((int) substr($lastProject->project_number ?? '0', -4)) + 1 : 1;
        
        return 'PRJ-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * حساب نسبة التقدم تلقائياً
     */
    public function calculateProgress(): int
    {
        if (!$this->stages || empty($this->stages)) {
            return 0;
        }

        // حساب التقدم من المهام
        $totalTasks = $this->tasks()->count();
        if ($totalTasks > 0) {
            $completedTasks = $this->tasks()->where('status', 'done')->count();
            return round(($completedTasks / $totalTasks) * 100);
        }

        // إذا لم توجد مهام، احسب من المراحل
        $totalStages = count($this->stages);
        $completedStages = $this->projectStages()
            ->where('status', 'مكتمل')
            ->count();

        return $totalStages > 0 ? round(($completedStages / $totalStages) * 100) : 0;
    }

    /**
     * حساب نسبة التقدم لكل مرحلة بناءً على المهام
     */
    public function calculateStageProgress($stageId): int
    {
        $stage = $this->projectStages()->find($stageId);
        if (!$stage) {
            return 0;
        }

        $tasks = $stage->tasks;
        if ($tasks->isEmpty()) {
            return 0;
        }

        $completedTasks = $tasks->where('status', 'done')->count();
        return round(($completedTasks / $tasks->count()) * 100);
    }

    /**
     * محادثة المشروع
     */
    public function conversation()
    {
        return $this->hasOne(Conversation::class)->where('type', 'project');
    }
}
