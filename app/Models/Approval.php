<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'client_id',
        'approvable_type',
        'approvable_id',
        'stage_key',
        'status',
        'client_note',
        'manager_note',
        'requested_by',
        'decided_by',
        'requested_at',
        'decided_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'decided_at' => 'datetime',
    ];

    /**
     * المشروع
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * العميل
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * العنصر المطلوب الموافقة عليه (Polymorphic)
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * من طلب الموافقة
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * من قرر الموافقة/الرفض
     */
    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /**
     * الحصول على اسم المرحلة بالعربية
     */
    public function getStageNameAttribute(): string
    {
        return match($this->stage_key) {
            'architectural' => 'معماري',
            'structural' => 'إنشائي',
            'electrical' => 'كهربائي',
            'mechanical' => 'ميكانيكي',
            'municipality' => 'بلدي',
            'health_environmental' => 'صحي/بيئي',
            'civil_defense' => 'دفاع مدني',
            default => $this->stage_key,
        };
    }

    /**
     * الحصول على الحالة بالعربية
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            default => $this->status,
        };
    }

    /**
     * التحقق من إمكانية الموافقة
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * التحقق من إمكانية الرفض
     */
    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }
}
