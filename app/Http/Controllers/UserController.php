<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index()
    {
        $users = User::all();

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
        $user = User::find($id);

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
        return response()->json([
            'message' => 'Data profil user berhasil diambil',
            'data' => $request->user(),
        ], 200);
    }

    /**
     * Get users by role
     */
    public function getByRole($role)
    {
        $users = User::where('role', $role)->get();

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
     * Update user data
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

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'nullable|in:director,manager,staff',
        ]);

        // Remove null values
        $validated = array_filter($validated, fn($value) => $value !== null);

        // Hash password if provided
        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Data user berhasil diperbarui',
            'data' => $user,
        ], 200);
    }

    /**
     * Delete user
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
     * Check if authenticated user can manage target user
     * Rules:
     * - Director (tingkat 1) bisa manage siapa saja
     * - Manager (tingkat 2) bisa manage staff (tingkat 3) saja
     * - Staff (tingkat 3) tidak bisa manage siapa saja
     */
    private function canManageUser($authUser, $targetUser)
    {
        // Director dapat manage siapa saja
        if ($authUser->role === 'director') {
            return true;
        }

        // Manager hanya bisa manage staff
        if ($authUser->role === 'manager' && $targetUser->role === 'staff') {
            return true;
        }

        // Staff tidak bisa manage siapa saja, dan tidak bisa manage diri sendiri jika bukan director
        return false;
    }
}
