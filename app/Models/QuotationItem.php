<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'item_name',
        'description',
        'qty',
        'unit',
        'unit_price',
        'line_total',
        'position',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'position' => 'integer',
    ];

    /**
     * المستند المرتبط
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * حساب الإجمالي تلقائياً
     */
    public function calculateLineTotal(): float
    {
        return round($this->qty * $this->unit_price, 2);
    }

    /**
     * تحديث الإجمالي تلقائياً
     */
    public function updateLineTotal(): void
    {
        $this->line_total = $this->calculateLineTotal();
    }
}
