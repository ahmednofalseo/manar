<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'type',
        'project_id',
        'user1_id',
        'user2_id',
        'title',
        'is_closed',
        'last_message_at',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    /**
     * المشروع (للشات الجماعي)
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * المستخدم الأول (للشات الفردي)
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * المستخدم الثاني (للشات الفردي)
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * الرسائل
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    /**
     * آخر رسالة
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * التحقق من إمكانية إرسال رسالة
     */
    public function canSendMessage(): bool
    {
        if ($this->is_closed) {
            return false;
        }

        // إذا كان الشات متعلق بمشروع، تحقق من حالة المشروع
        if ($this->type === 'project' && $this->project) {
            return $this->project->status !== 'مكتمل';
        }

        return true;
    }

    /**
     * إغلاق الشات
     */
    public function close(): void
    {
        $this->update(['is_closed' => true]);
    }

    /**
     * فتح الشات
     */
    public function open(): void
    {
        $this->update(['is_closed' => false]);
    }

    /**
     * الحصول على المشاركين في المحادثة
     */
    public function getParticipantsAttribute()
    {
        if ($this->type === 'project') {
            $participants = collect();
            
            // إضافة مدير المشروع
            if ($this->project->project_manager_id) {
                $participants->push($this->project->projectManager);
            }
            
            // إضافة أعضاء الفريق
            if ($this->project->team_members && is_array($this->project->team_members)) {
                $teamMembers = User::whereIn('id', $this->project->team_members)->get();
                $participants = $participants->merge($teamMembers);
            }
            
            return $participants->unique('id')->values();
        } else {
            // للشات الفردي
            return collect([$this->user1, $this->user2])->filter();
        }
    }
}
