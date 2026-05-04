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
        return match ($this) {
            self::UNPAID => __('Unpaid'),
            self::PARTIAL => __('Partial'),
            self::PAID => __('Paid'),
            self::OVERDUE => __('Overdue'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'red',
            self::PARTIAL => 'yellow',
            self::PAID => 'green',
            self::OVERDUE => 'orange',
        };
    }
}
