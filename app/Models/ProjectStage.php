<?php

namespace App\Models;

use App\Enums\ProjectStageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'stage_name',
        'sort_order',
        'invoice_amount',
        'status',
        'start_date',
        'end_date',
        'progress',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress' => 'integer',
        'sort_order' => 'integer',
        'invoice_amount' => 'decimal:2',
        'status' => ProjectStageStatus::class,
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * مهام المرحلة
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_stage_id');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'project_stage_id');
    }

    public function isWorkable(): bool
    {
        return $this->status === ProjectStageStatus::Active;
    }
}
