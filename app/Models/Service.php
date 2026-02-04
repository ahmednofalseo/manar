<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'category_id',
        'parent_id',
        'is_custom',
        'has_sub_services',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'has_sub_services' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * الفئة التابعة لها
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * الخدمة الرئيسية (لخدمات بلدي الفرعية)
     */
    public function parent()
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    /**
     * الخدمات الفرعية
     */
    public function subServices()
    {
        return $this->hasMany(Service::class, 'parent_id')->orderBy('order');
    }

    /**
     * قوالب المسارات
     */
    public function workflowTemplates()
    {
        return $this->hasMany(WorkflowTemplate::class);
    }

    /**
     * القالب الافتراضي
     */
    public function defaultWorkflowTemplate()
    {
        return $this->hasOne(WorkflowTemplate::class)->where('is_default', true);
    }

    /**
     * المشاريع المرتبطة
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * مسارات المشاريع
     */
    public function projectWorkflows()
    {
        return $this->hasMany(ProjectWorkflow::class);
    }

    /**
     * الخدمات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الخدمات الرئيسية فقط (ليست فرعية)
     */
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * الخدمات المخصصة
     */
    public function scopeCustom($query)
    {
        return $query->where('is_custom', true);
    }
}
