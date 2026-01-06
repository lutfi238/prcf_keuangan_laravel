<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'used',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
        ];
    }

    /**
     * User yang memiliki OTP ini
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Cek apakah OTP masih valid
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Generate OTP baru untuk user
     */
    public static function generateFor(User $user, int $length = 6, int $ttlSeconds = 60): self
    {
        // Invalidate existing unused OTPs
        self::where('user_id', $user->id_user)
            ->where('used', false)
            ->update(['used' => true]);

        // Generate new OTP
        $code = str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);

        return self::create([
            'user_id' => $user->id_user,
            'code' => $code,
            'expires_at' => now()->addSeconds($ttlSeconds),
            'used' => false,
        ]);
    }

    /**
     * Verify OTP code
     */
    public static function verify(User $user, string $code): bool
    {
        $otp = self::where('user_id', $user->id_user)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if ($otp) {
            $otp->update(['used' => true]);
            return true;
        }

        return false;
    }
}