<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nama',
        'role',
        'email',
        'no_HP',
        'password',
        'status',
        'email_verified_at',
        'last_notification_check',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_notification_check' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * OTP codes for this user
     */
    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class, 'user_id', 'id_user');
    }

    /**
     * Proposals approved by this FM user
     */
    public function approvedProposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'approved_by_fm', 'id_user');
    }

    /**
     * Financial reports created by this user
     */
    public function createdReports(): HasMany
    {
        return $this->hasMany(LaporanKeuanganHeader::class, 'created_by', 'id_user');
    }

    /**
     * Financial reports verified by this SA user
     */
    public function verifiedReports(): HasMany
    {
        return $this->hasMany(LaporanKeuanganHeader::class, 'verified_by', 'id_user');
    }

    /**
     * Financial reports approved by this FM user
     */
    public function approvedReports(): HasMany
    {
        return $this->hasMany(LaporanKeuanganHeader::class, 'approved_by', 'id_user');
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if user is Finance Manager
     */
    public function isFinanceManager(): bool
    {
        return $this->role === UserRole::FinanceManager;
    }

    /**
     * Check if user is Project Manager
     */
    public function isProjectManager(): bool
    {
        return $this->role === UserRole::ProjectManager;
    }

    /**
     * Check if user is Staff Accountant
     */
    public function isStaffAccountant(): bool
    {
        return $this->role === UserRole::StaffAccountant;
    }

    /**
     * Check if user is Direktur
     */
    public function isDirektur(): bool
    {
        return $this->role === UserRole::Direktur;
    }

    /**
     * Get dashboard route for this user's role
     */
    public function getDashboardRoute(): string
    {
        return $this->role->dashboardRoute();
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user account is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user account is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Get notifications for this user
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'id_user');
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    /**
     * Get recent notifications
     */
    public function recentNotifications(int $limit = 10)
    {
        return $this->notifications()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}