<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    case CHECK = 'check';
    case ELECTRONIC = 'electronic';

    public function label(): string
    {
        return match($this) {
            self::CASH => 'نقدي',
            self::TRANSFER => 'تحويل بنكي',
            self::CHECK => 'شيك',
            self::ELECTRONIC => 'إلكتروني',
        };
    }
}
