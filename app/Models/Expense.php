<?php

namespace App\Models;

use App\Enums\ExpenseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'voucher_number',
        'date',
        'department',
        'type',
        'description',
        'amount',
        'payment_method',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'rejected_by',
        'rejection_reason',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'status' => ExpenseStatus::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * منشئ المصروف
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * من اعتمد المصروف
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * من رفض المصروف
     */
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * المرفقات
     */
    public function attachments()
    {
        return $this->hasMany(ExpenseAttachment::class)->latest();
    }

    /**
     * توليد رقم سند تلقائي
     */
    public static function generateVoucherNumber(): string
    {
        $year = date('Y');
        $lastExpense = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastExpense ? ((int) substr($lastExpense->voucher_number ?? '0', -4)) + 1 : 1;
        
        return 'EXP-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor للحالة
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * التحقق من وجود مرفقات
     */
    public function getHasAttachmentsAttribute(): bool
    {
        return $this->attachments()->count() > 0;
    }
}
