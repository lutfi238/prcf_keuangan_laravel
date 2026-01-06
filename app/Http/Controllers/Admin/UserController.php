<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.form', [
            'user' => null,
            'roles' => UserRole::cases(),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_HP' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', UserRole::values()),
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'status' => 'required|in:active,pending,inactive',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_HP' => $request->no_HP,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'no_HP' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', UserRole::values()),
            'status' => 'required|in:active,pending,inactive',
        ];

        // Only require password if provided
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        // Last admin protection: prevent demoting the last admin
        if ($user->isAdmin() && $request->role !== 'Admin') {
            $adminCount = User::where('role', 'Admin')->where('status', 'active')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat mengubah role admin terakhir. Minimal harus ada 1 Admin aktif.');
            }
        }

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'no_HP' => $request->no_HP,
            'role' => $request->role,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Last admin protection
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'Admin')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus admin terakhir. Minimal harus ada 1 Admin.');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Activate a pending user.
     */
    public function activate(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('success', "User {$user->nama} berhasil diaktifkan.");
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(User $user)
    {
        // Prevent self-deactivation
        if ($user->id_user === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        // Last admin protection
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'Admin')->where('status', 'active')->count();
            if ($adminCount <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan admin terakhir. Minimal harus ada 1 Admin aktif.');
            }
        }

        $user->update(['status' => 'inactive']);

        return back()->with('success', "User {$user->nama} berhasil dinonaktifkan.");
    }
}
