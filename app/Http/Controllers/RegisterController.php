<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Models\Position;
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

        // Get director position
        $directorPosition = Position::where('code', 'director')->first();

        $director = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'position_id' => $directorPosition?->id,
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

        // Validate input - support both legacy role and new position_id
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'position_id' => 'nullable|exists:positions,id',
            'role' => 'nullable|in:manager,staff', // Legacy support
        ]);

        // Determine position_id
        $positionId = null;
        if ($validated['position_id']) {
            $position = Position::find($validated['position_id']);

            // Check if creator can assign this position
            if ($creator->isDirector()) {
                // Director can assign any position
                $positionId = $position->id;
            } elseif ($creator->isManager()) {
                // Manager can only assign level 3 positions
                if ($position->level !== 3) {
                    throw ValidationException::withMessages([
                        'position_id' => ['Manager hanya dapat membuat Staff (Level 3).'],
                    ]);
                }
                $positionId = $position->id;
            }
        } elseif ($validated['role']) {
            // Legacy: Map role to default position
            $positionMap = [
                'manager' => Position::where('code', 'manager')->first()?->id,
                'staff' => Position::where('code', 'staff')->first()?->id,
            ];

            if ($creator->isManager() && $validated['role'] !== 'staff') {
                throw ValidationException::withMessages([
                    'role' => ['Manager hanya dapat membuat akun Staff.'],
                ]);
            }

            $positionId = $positionMap[$validated['role']] ?? null;
        }

        // Generate default password
        $tempPassword = $this->generateDefaultPassword();

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'position_id' => $positionId,
            'role' => $validated['role'] ?? 'staff', // Legacy support
            'temp_password' => $tempPassword,
            'temp_password_set_at' => now(),
        ]);

        // Send welcome email with credentials
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user, $tempPassword));
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
                'position_id' => $user->position_id,
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

        // Get manager position
        $managerPosition = Position::where('code', 'manager')->first();

        $tempPassword = $this->generateDefaultPassword();

        $manager = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'position_id' => $managerPosition?->id,
            'role' => 'manager',
            'temp_password' => $tempPassword,
            'temp_password_set_at' => now(),
        ]);

        try {
            Mail::to($manager->email)->send(new WelcomeEmail($manager, $tempPassword));
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Akun Manager berhasil dibuat dan email telah dikirim',
            'user' => [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'position_id' => $manager->position_id,
                'role' => $manager->role,
            ],
        ], 201);
    }
}
