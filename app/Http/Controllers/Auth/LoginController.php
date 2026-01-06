<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request (Step 1: Email/Password)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Check user status
        if (!$user->isActive()) {
            if ($user->isPending()) {
                return redirect()->route('auth.pending')
                    ->with('info', 'Akun Anda sedang menunggu persetujuan Admin.');
            }
            throw ValidationException::withMessages([
                'email' => ['Akun Anda tidak aktif. Hubungi Administrator.'],
            ]);
        }

        // Check if OTP should be skipped (for development/testing)
        if (config('prcf.skip_otp', false)) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended(route($user->getDashboardRoute()));
        }

        // Generate and send OTP
        $otp = OtpCode::generateFor($user);
        
        // Send OTP via email
        $this->sendOtpEmail($user, $otp->code);

        // Store user ID in session for OTP verification
        session(['otp_user_id' => $user->id_user]);
        session(['otp_email' => $user->email]);

        return redirect()->route('auth.verify-otp')
            ->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('auth.verify-otp', [
            'email' => session('otp_email'),
        ]);
    }

    /**
     * Verify OTP (Step 2)
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi expired. Silakan login ulang.');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        // Verify OTP
        if (!OtpCode::verify($user, $request->otp)) {
            throw ValidationException::withMessages([
                'otp' => ['Kode OTP salah atau sudah expired.'],
            ]);
        }

        // Clear OTP session
        session()->forget(['otp_user_id', 'otp_email']);

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route($user->getDashboardRoute()))
            ->with('success', 'Login berhasil!');
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')
                ->with('error', 'Sesi expired. Silakan login ulang.');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User tidak ditemukan.');
        }

        // Generate new OTP
        $otp = OtpCode::generateFor($user);
        
        // Send OTP via email
        $this->sendOtpEmail($user, $otp->code);

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }

    /**
     * Send OTP via email
     */
    private function sendOtpEmail(User $user, string $code): void
    {
        $subject = 'Kode OTP Login - PRCF Keuangan';
        $message = "Halo {$user->nama},\n\n";
        $message .= "Kode OTP Anda untuk login ke PRCF Keuangan adalah:\n\n";
        $message .= "**{$code}**\n\n";
        $message .= "Kode ini berlaku selama 60 detik.\n\n";
        $message .= "Jika Anda tidak melakukan permintaan ini, abaikan email ini.\n\n";
        $message .= "Salam,\nTim PRCF Keuangan";

        try {
            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            // Log error but don't fail the request
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
        }
    }
}