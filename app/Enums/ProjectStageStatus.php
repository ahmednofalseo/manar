<?php

namespace App\Enums;

enum ProjectStageStatus: string
{
    case Locked = 'locked';
    case Active = 'active';
    case AwaitingPayment = 'awaiting_payment';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Locked => __('Project stage status locked'),
            self::Active => __('Project stage status active'),
            self::AwaitingPayment => __('Project stage status awaiting payment'),
            self::Completed => __('Project stage status completed'),
        };
    }

    public function isWorkable(): bool
    {
        return $this === self::Active;
    }
}
