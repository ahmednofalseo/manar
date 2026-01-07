<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'client_id',
        'number',
        'issue_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'status' => InvoiceStatus::class,
        'payment_method' => PaymentMethod::class,
    ];

    /**
     * المشروع
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * العميل
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * الدفعات
     */
    public function payments()
    {
        return $this->hasMany(Payment::class)->orderBy('paid_at', 'desc');
    }

    /**
     * تحديث المبلغ المدفوع والحالة
     */
    public function updatePaidAmountAndStatus(): void
    {
        $paidAmount = $this->payments()
            ->where('status', \App\Enums\PaymentStatus::PAID)
            ->sum('amount');

        $this->paid_amount = $paidAmount;

        // تحديث الحالة
        if ($paidAmount >= $this->total_amount) {
            $this->status = InvoiceStatus::PAID;
        } elseif ($paidAmount > 0) {
            $this->status = InvoiceStatus::PARTIAL;
        } else {
            // التحقق من التأخير
            if ($this->due_date < now() && $this->status !== InvoiceStatus::PAID) {
                $this->status = InvoiceStatus::OVERDUE;
            } else {
                $this->status = InvoiceStatus::UNPAID;
            }
        }

        $this->save();
    }

    /**
     * توليد رقم فاتورة تلقائي
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastInvoice ? ((int) substr($lastInvoice->number ?? '0', -4)) + 1 : 1;
        
        return 'INV-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor للحالة
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Accessor لطريقة الدفع
     */
    public function getPaymentMethodLabelAttribute(): ?string
    {
        return $this->payment_method?->label();
    }

    /**
     * المبلغ المتبقي
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }
}
