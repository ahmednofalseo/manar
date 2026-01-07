<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    public function label(): string
    {
        return match($this) {
            self::UNPAID => 'غير مدفوعة',
            self::PARTIAL => 'جزئية',
            self::PAID => 'مدفوعة',
            self::OVERDUE => 'متأخرة',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::UNPAID => 'red',
            self::PARTIAL => 'yellow',
            self::PAID => 'green',
            self::OVERDUE => 'orange',
        };
    }
}
