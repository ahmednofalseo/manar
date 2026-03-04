<?php

namespace App\Enums;

enum ProjectStageKey: string
{
    case ARCHITECTURAL = 'architectural';
    case STRUCTURAL = 'structural';
    case ELECTRICAL = 'electrical';
    case MECHANICAL = 'mechanical';
    case MUNICIPALITY = 'municipality';
    case HEALTH_ENVIRONMENTAL = 'health_environmental';
    case CIVIL_DEFENSE = 'civil_defense';

    public function label(): string
    {
        return match($this) {
            self::ARCHITECTURAL => 'معماري',
            self::STRUCTURAL => 'إنشائي',
            self::ELECTRICAL => 'كهربائي',
            self::MECHANICAL => 'ميكانيكي',
            self::MUNICIPALITY => 'بلدي',
            self::HEALTH_ENVIRONMENTAL => 'صحي/بيئي',
            self::CIVIL_DEFENSE => 'دفاع مدني',
        };
    }

    /**
     * الحصول على جميع المراحل
     */
    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * الحصول على جميع المراحل مع أسمائها
     */
    public static function allWithLabels(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}






