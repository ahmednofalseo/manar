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
        return match ($this) {
            self::CASH => __('Payment method cash'),
            self::TRANSFER => __('Payment method bank transfer'),
            self::CHECK => __('Payment method check'),
            self::ELECTRONIC => __('Payment method electronic'),
        };
    }
}
