<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index()
    {
        $users = User::with('position')->get();

        return response()->json([
            'message' => 'Data user berhasil diambil',
            'data' => $users,
            'total' => $users->count(),
        ], 200);
    }

    /**
     * Get user by ID
     */
    public function show($id)
    {
        $user = User::with('position')->find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'message' => 'Data user berhasil diambil',
            'data' => $user,
        ], 200);
    }

    /**
     * Get current authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = User::with('position')->find($request->user()->id);

        return response()->json([
            'message' => 'Data profil user berhasil diambil',
            'data' => $user,
        ], 200);
    }

    /**
     * Update current authenticated user profile
     * POST /api/users/profile/update
     */
    public function updateProfile(Request $request)
    {
        $user = User::find($request->user()->id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }

        if ($request->filled('email')) {
            $user->email = $request->input('email');
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $photoPath;
        }

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => $user->load('position'),
        ], 200);
    }

    /**
     * Change password for authenticated user
     * POST /api/users/change-password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'message' => 'Password saat ini tidak sesuai',
            ], 422);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah',
        ], 200);
    }

    /**
     * Get users by role
     */
    public function getByRole($role)
    {
        $users = User::with('position')->where('role', $role)->get();

        if ($users->isEmpty()) {
            return response()->json([
                'message' => "User dengan role '$role' tidak ditemukan",
                'data' => [],
            ], 404);
        }

        return response()->json([
            'message' => "Data user dengan role '$role' berhasil diambil",
            'data' => $users,
            'total' => $users->count(),
        ], 200);
    }

    /**
     * Update user data via POST
     * POST /api/users/{id}/update
     */
    public function update(Request $request, $id)
    {
        $authUser = $request->user();
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        // Check authorization
        if (!$this->canManageUser($authUser, $user)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengedit user ini',
            ], 403);
        }

        // Validate input
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'nullable|in:director,manager,staff',
            'position_id' => 'nullable|exists:positions,id',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Check authorization for role/position promotion
        if ($request->filled('position_id') || $request->filled('role')) {
            $newPositionId = $request->filled('position_id') ? $request->input('position_id') : $user->position_id;
            if (!$this->canPromoteUser($authUser, $user, $newPositionId)) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin untuk mempromosikan user ini ke level tersebut',
                ], 403);
            }
        }

        // Update name if provided
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }

        // Update email if provided
        if ($request->filled('email')) {
            $user->email = $request->input('email');
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        // Update role if provided
        if ($request->filled('role')) {
            $user->role = $request->input('role');
        }

        // Update position if provided
        if ($request->filled('position_id')) {
            $user->position_id = $request->input('position_id');
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && file_exists(storage_path('app/public/' . $user->profile_photo))) {
                unlink(storage_path('app/public/' . $user->profile_photo));
            }

            // Store new photo
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $photoPath;
        }

        $user->save();

        return response()->json([
            'message' => 'Data user berhasil diperbarui',
            'data' => $user,
        ], 200);
    }

    /**
     * Delete user via POST
     * POST /api/users/{id}/delete
     */
    public function destroy(Request $request, $id)
    {
        $authUser = $request->user();
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        // Check authorization
        if (!$this->canManageUser($authUser, $user)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus user ini',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus',
            'data' => $user,
        ], 200);
    }

    /**
     * Check if authenticated user can promote target user
     * Rules:
     * - Level 1 (Director) bisa promosi ke level manapun
     * - Level 2 (Manager) hanya bisa promosi ke level 3 (tidak bisa promosi ke level 1 atau 2)
     * - Level 3 (Staff) tidak bisa promosi siapa saja
     */
    private function canPromoteUser($authUser, $targetUser, $newPositionId)
    {
        // Get the level of new position
        $newPosition = Position::find($newPositionId);
        if (!$newPosition) {
            return false;
        }

        $authLevel = $authUser->getLevel();
        $newLevel = $newPosition->level;

        // Level 1 (Director) dapat promosi ke level manapun
        if ($authLevel === 1) {
            return true;
        }

        // Level 2 (Manager) hanya bisa promosi target user ke level 3 (tidak bisa ke level 1 dan 2)
        if ($authLevel === 2 && $newLevel === 3) {
            return true;
        }

        // Level 3 (Staff) dan permission lainnya tidak diizinkan
        return false;
    }

    /**
     * Check if authenticated user can manage target user
     * Rules:
     * - Setiap user bisa edit akun sendiri
     * - Level 1 (Director) bisa manage semua user (level 2 dan 3)
     * - Level 2 (Manager) tidak bisa manage level 1 dan level 2 (satu level), hanya bisa manage level 3 (staff)
     * - Level 3 (Staff) hanya bisa edit akun sendiri
     */
    private function canManageUser($authUser, $targetUser)
    {
        // User bisa edit akun sendiri
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        $authLevel = $authUser->getLevel();
        $targetLevel = $targetUser->getLevel();

        // Level 1 (Director) dapat manage siapa saja (level 2 dan 3)
        if ($authLevel === 1) {
            return true;
        }

        // Level 2 (Manager) hanya bisa manage level 3 (staff), tidak bisa manage level 1 dan level 2
        if ($authLevel === 2 && $targetLevel === 3) {
            return true;
        }

        // Level 3 (Staff) dan permission lainnya tidak diizinkan
        return false;
    }
}
