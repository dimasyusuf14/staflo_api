<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * Generate default password with year
     * Format: ?Staflo{YEAR}!
     */
    private function generateDefaultPassword(): string
    {
        $year = date('Y');
        return "?Staflo{$year}!";
    }

    /**
     * Register director (for initial setup)
     * Direktur bisa sign up pertama kali
     */
    public function registerDirector(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $director = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'director',
        ]);

        return response()->json([
            'message' => 'Akun Direktur berhasil dibuat',
            'user' => $director,
        ], 201);
    }

    /**
     * Create user account (Director & Manager only)
     * Direktur dan Manager bisa membuat akun user dengan role apapun
     */
    public function createUser(Request $request)
    {
        // Verify user is authenticated
        $creator = $request->user();
        if (!$creator || !$creator->canCreateAccounts()) {
            throw ValidationException::withMessages([
                'authorization' => ['Anda tidak memiliki izin untuk membuat akun.'],
            ]);
        }

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:manager,staff',
        ]);

        // Manager hanya bisa create staff
        if ($creator->isManager() && $validated['role'] !== 'staff') {
            throw ValidationException::withMessages([
                'role' => ['Manager hanya dapat membuat akun Staff.'],
            ]);
        }

        // Generate default password
        $tempPassword = $this->generateDefaultPassword();

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => $validated['role'],
            'temp_password' => $tempPassword,
            'temp_password_set_at' => now(),
        ]);

        // Send welcome email with credentials
        try {
            Mail::send(new WelcomeEmail($user, $tempPassword));
        } catch (\Exception $e) {
            // Log error tapi jangan fail, user sudah dibuat
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Akun berhasil dibuat dan email telah dikirim',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_by' => $creator->name,
            ],
        ], 201);
    }

    /**
     * Register manager account (Director only)
     * Direktur bisa membuat akun manager tanpa email
     */
    public function registerManager(Request $request)
    {
        $creator = $request->user();
        if (!$creator || !$creator->isDirector()) {
            throw ValidationException::withMessages([
                'authorization' => ['Hanya Direktur yang dapat membuat akun Manager.'],
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ]);

        $tempPassword = $this->generateDefaultPassword();

        $manager = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'manager',
            'temp_password' => $tempPassword,
            'temp_password_set_at' => now(),
        ]);

        try {
            Mail::send(new WelcomeEmail($manager, $tempPassword));
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Akun Manager berhasil dibuat dan email telah dikirim',
            'user' => [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'role' => $manager->role,
            ],
        ], 201);
    }
}
