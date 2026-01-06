<?php

namespace App\Enums;

enum ProposalStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case ApprovedFM = 'approved_fm';
    case Approved = 'approved';
    case Rejected = 'rejected';

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
            self::Submitted => 'Menunggu Persetujuan FM',
            self::ApprovedFM => 'Disetujui FM',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
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
            self::ApprovedFM => 'green',
            self::Approved => 'green',
            self::Rejected => 'red',
        };
    }

    /**
     * Check if proposal is editable
     */
    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    /**
     * Check if proposal is final
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::ApprovedFM, self::Approved, self::Rejected]);
    }
}