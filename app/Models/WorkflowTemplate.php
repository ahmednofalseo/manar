<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * الخدمة المرتبطة
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * خطوات المسار
     */
    public function steps()
    {
        return $this->hasMany(WorkflowStep::class)->orderBy('order');
    }

    /**
     * مسارات المشاريع المستخدمة لهذا القالب
     */
    public function projectWorkflows()
    {
        return $this->hasMany(ProjectWorkflow::class);
    }

    /**
     * القوالب النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
