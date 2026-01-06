<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak ditemukan dalam sistem.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate token
        $token = Str::random(64);

        // Delete existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Insert new token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Send email with reset link
        try {
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);
            
            $message = "Halo {$user->nama},\n\n";
            $message .= "Anda menerima email ini karena ada permintaan reset password untuk akun Anda.\n\n";
            $message .= "Klik link berikut untuk reset password:\n";
            $message .= $resetUrl . "\n\n";
            $message .= "Link ini berlaku selama 60 menit.\n\n";
            $message .= "Jika Anda tidak melakukan permintaan ini, abaikan email ini.\n\n";
            $message .= "Salam,\nTim PRCF Keuangan";

            Mail::raw($message, function ($mail) use ($user) {
                $mail->to($user->email)
                    ->subject('Reset Password - PRCF Keuangan');
            });

            return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }
    }

    /**
     * Show reset password form
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle password reset
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Check token
        $tokenRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRecord) {
            return back()->with('error', 'Token reset password tidak valid.');
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', 'Token reset password sudah expired.');
        }

        // Verify token
        if (!Hash::check($request->token, $tokenRecord->token)) {
            return back()->with('error', 'Token reset password tidak valid.');
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
