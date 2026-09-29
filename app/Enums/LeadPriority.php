<?php

namespace App\Enums;

enum LeadPriority: int
{
    case LOW = 10;
    case NORMAL = 20;
    case HIGH = 30;
    case URGENT = 40;

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::NORMAL => 'Normal',
            self::HIGH => 'High',
            self::URGENT => 'Urgent',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => '#6c757d',
            self::NORMAL => '#0d6efd',
            self::HIGH => '#f59f00',
            self::URGENT => '#dc3545',
        };
    }
}
