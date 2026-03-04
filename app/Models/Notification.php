<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'title',
        'message',
        'data',
        'read',
        'read_at',
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * المستخدم المستهدف
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الكائن المرتبط (Task)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * تحديد الإشعار كمقروء
     */
    public function markAsRead()
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
    }
}
