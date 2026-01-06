<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'link',
        'related_id',
        'related_type',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    /**
     * User who receives this notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): bool
    {
        $this->is_read = true;
        return $this->save();
    }

    /**
     * Get unread notifications for a user
     */
    public static function unreadFor(User $user)
    {
        return static::where('user_id', $user->id_user)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread count for a user
     */
    public static function unreadCountFor(User $user): int
    {
        return static::where('user_id', $user->id_user)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Create notification for proposal submission
     */
    public static function proposalSubmitted(Proposal $proposal, User $targetUser): static
    {
        return static::create([
            'user_id' => $targetUser->id_user,
            'type' => 'proposal_submitted',
            'message' => "Proposal baru '{$proposal->judul_proposal}' memerlukan review Anda.",
            'link' => route('proposals.show', $proposal->id_proposal),
            'related_id' => $proposal->id_proposal,
            'related_type' => 'proposal',
        ]);
    }

    /**
     * Create notification for proposal approval
     */
    public static function proposalApproved(Proposal $proposal, User $targetUser, string $stage = 'fm'): static
    {
        $stageLabel = $stage === 'fm' ? 'Finance Manager' : 'Direktur';
        return static::create([
            'user_id' => $targetUser->id_user,
            'type' => 'proposal_approved',
            'message' => "Proposal '{$proposal->judul_proposal}' telah disetujui oleh {$stageLabel}.",
            'link' => route('proposals.show', $proposal->id_proposal),
            'related_id' => $proposal->id_proposal,
            'related_type' => 'proposal',
        ]);
    }

    /**
     * Create notification for proposal rejection
     */
    public static function proposalRejected(Proposal $proposal, User $targetUser): static
    {
        return static::create([
            'user_id' => $targetUser->id_user,
            'type' => 'proposal_rejected',
            'message' => "Proposal '{$proposal->judul_proposal}' telah ditolak.",
            'link' => route('proposals.show', $proposal->id_proposal),
            'related_id' => $proposal->id_proposal,
            'related_type' => 'proposal',
        ]);
    }

    /**
     * Create notification for report verification
     */
    public static function reportVerified(LaporanKeuanganHeader $report, User $targetUser): static
    {
        return static::create([
            'user_id' => $targetUser->id_user,
            'type' => 'report_verified',
            'message' => "Laporan keuangan '{$report->nama_kegiatan}' telah diverifikasi Staff Accountant.",
            'link' => route('reports.show', $report->id_laporan_keu),
            'related_id' => $report->id_laporan_keu,
            'related_type' => 'report',
        ]);
    }

    /**
     * Notify all users with specific role
     */
    public static function notifyRole(string $role, string $type, string $message, ?string $link = null): void
    {
        $users = User::where('role', $role)->where('status', 'active')->get();
        
        foreach ($users as $user) {
            static::create([
                'user_id' => $user->id_user,
                'type' => $type,
                'message' => $message,
                'link' => $link,
            ]);
        }
    }
}
