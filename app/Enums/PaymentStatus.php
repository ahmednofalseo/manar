<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PAID = 'paid';
    case PENDING = 'pending';

    public function label(): string
    {
        return match($this) {
            self::PAID => 'مدفوع',
            self::PENDING => 'قيد الانتظار',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PAID => 'green',
            self::PENDING => 'yellow',
        };
    }
}
