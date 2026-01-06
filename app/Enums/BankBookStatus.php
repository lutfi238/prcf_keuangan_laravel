<?php

namespace App\Enums;

enum BankBookStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';

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
            self::Draft => 'Draft',
            self::Submitted => 'Diajukan',
            self::Approved => 'Disetujui',
        };
    }

    /**
     * Get badge color for UI
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::Draft => 'gray',
            self::Submitted => 'yellow',
            self::Approved => 'green',
        };
    }
}