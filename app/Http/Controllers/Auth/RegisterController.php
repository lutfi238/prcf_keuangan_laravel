<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        if (!config('prcf.registration.enabled', true)) {
            return redirect()->route('login')
                ->with('error', 'Registrasi saat ini ditutup.');
        }

        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        if (!config('prcf.registration.enabled', true)) {
            return redirect()->route('login')
                ->with('error', 'Registrasi saat ini ditutup.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_HP' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Format phone number
        $phone = $this->formatPhoneNumber($request->no_HP);

        // Determine initial status
        $requireApproval = config('prcf.registration.require_admin_approval', true);
        $status = $requireApproval ? 'pending' : 'active';

        // Create user
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_HP' => $phone,
            'password' => Hash::make($request->password),
            'role' => config('prcf.registration.default_role', 'Project Manager'),
            'status' => $status,
        ]);

        // Notify admins about new registration
        if ($requireApproval) {
            $this->notifyAdmins($user);
        }

        if ($requireApproval) {
            return redirect()->route('auth.pending')
                ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu persetujuan Admin.');
        }

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /**
     * Format phone number to Indonesian format
     */
    private function formatPhoneNumber(?string $phone): ?string
    {
        if (!$phone) return null;

        // Remove non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Convert 08xx to 628xx
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Remove leading +
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        // Add 62 if doesn't start with it
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Notify admins about new registration
     */
    private function notifyAdmins(User $newUser): void
    {
        $admins = User::where('role', UserRole::Admin)
            ->where('status', 'active')
            ->get();

        foreach ($admins as $admin) {
            try {
                $message = "User baru mendaftar:\n\n";
                $message .= "Nama: {$newUser->nama}\n";
                $message .= "Email: {$newUser->email}\n";
                $message .= "No HP: {$newUser->no_HP}\n\n";
                $message .= "Silakan login ke sistem untuk menyetujui akun ini.";

                Mail::raw($message, function ($mail) use ($admin) {
                    $mail->to($admin->email)
                        ->subject('Pendaftaran User Baru - PRCF Keuangan');
                });
            } catch (\Exception $e) {
                \Log::error('Failed to notify admin about new registration: ' . $e->getMessage());
            }
        }
    }
}
