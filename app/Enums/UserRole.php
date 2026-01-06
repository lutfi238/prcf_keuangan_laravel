<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'Admin';
    case FinanceManager = 'Finance Manager';
    case ProjectManager = 'Project Manager';
    case StaffAccountant = 'Staff Accountant';
    case Direktur = 'Direktur';

    /**
     * Get all role values as array
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
            self::Admin => 'Administrator',
            self::FinanceManager => 'Finance Manager',
            self::ProjectManager => 'Project Manager',
            self::StaffAccountant => 'Staff Accountant',
            self::Direktur => 'Direktur',
        };
    }

    /**
     * Get dashboard route for this role
     */
    public function dashboardRoute(): string
    {
        return match($this) {
            self::Admin => 'dashboard.admin',
            self::FinanceManager => 'dashboard.fm',
            self::ProjectManager => 'dashboard.pm',
            self::StaffAccountant => 'dashboard.sa',
            self::Direktur => 'dashboard.dir',
        };
    }

    /**
     * Check if role can approve proposals
     */
    public function canApproveProposals(): bool
    {
        return in_array($this, [self::FinanceManager]);
    }

    /**
     * Check if role can verify reports
     */
    public function canVerifyReports(): bool
    {
        return in_array($this, [self::StaffAccountant]);
    }

    /**
     * Check if role can approve reports
     */
    public function canApproveReports(): bool
    {
        return in_array($this, [self::FinanceManager]);
    }

    /**
     * Check if role can manage users
     */
    public function canManageUsers(): bool
    {
        return $this === self::Admin;
    }
}