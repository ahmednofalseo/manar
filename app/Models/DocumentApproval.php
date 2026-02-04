<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'approved_by',
        'action',
        'reason',
    ];

    /**
     * المستند المرتبط
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * المعتمد
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
