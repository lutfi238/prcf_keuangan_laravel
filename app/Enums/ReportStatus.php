<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Verified = 'verified';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case RevisionRequested = 'revision_requested';

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
            self::Submitted => 'Menunggu Verifikasi SA',
            self::Verified => 'Diverifikasi SA',
            self::Approved => 'Disetujui FM',
            self::Rejected => 'Ditolak',
            self::RevisionRequested => 'Perlu Revisi',
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
            self::Verified => 'blue',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::RevisionRequested => 'orange',
        };
    }

    /**
     * Get next status in workflow
     */
    public function nextStatus(): ?self
    {
        return match($this) {
            self::Draft => self::Submitted,
            self::Submitted => self::Verified,
            self::Verified => self::Approved,
            default => null,
        };
    }

    /**
     * Check if report is editable
     */
    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::RevisionRequested]);
    }
}