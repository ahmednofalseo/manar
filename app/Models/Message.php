<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'message',
        'attachment',
        'attachment_name',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * المحادثة
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * المستخدم الذي أرسل الرسالة
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تحديد الرسالة كمقروءة
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * الحصول على رابط المرفق
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? Storage::url($this->attachment) : null;
    }

    /**
     * الحصول على اسم الملف المرفق
     * إذا كان attachment_name محفوظ في قاعدة البيانات، استخدمه
     * وإلا استخدم اسم الملف من المسار
     */
    public function getAttachmentNameAttribute(): ?string
    {
        // إذا كان هناك attachment_name محفوظ، استخدمه
        if (isset($this->attributes['attachment_name']) && $this->attributes['attachment_name']) {
            return $this->attributes['attachment_name'];
        }
        
        // وإلا استخدم اسم الملف من المسار
        return $this->attachment ? basename($this->attachment) : null;
    }
}
