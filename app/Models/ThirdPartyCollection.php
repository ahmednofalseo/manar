<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThirdPartyCollection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'third_party_name',
        'collected_amount',
        'collected_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'collected_amount' => 'decimal:2',
        'collected_at' => 'date',
    ];

    /**
     * المشروع
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * منشئ السجل
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
