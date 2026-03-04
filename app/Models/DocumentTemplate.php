<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'content',
        'variables',
        'is_active',
        'order',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * المستندات التي تستخدم هذا القالب
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'template_id');
    }

    /**
     * الحصول على القوالب النشطة حسب النوع
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
