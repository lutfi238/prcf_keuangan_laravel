<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Planning = 'planning';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * Get all status values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for display
     */
    public function label(): string
    {
        return match($this) {
            self::Planning => 'Perencanaan',
            self::Ongoing => 'Berlangsung',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Get badge color for UI
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::Planning => 'blue',
            self::Ongoing => 'green',
            self::Completed => 'gray',
            self::Cancelled => 'red',
        };
    }

    /**
     * Check if project is active
     */
    public function isActive(): bool
    {
        return in_array($this, [self::Planning, self::Ongoing]);
    }
}