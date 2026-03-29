<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Get all positions
     */
    public function index()
    {
        $positions = Position::where('is_active', true)
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Data posisi berhasil diambil',
            'data' => $positions,
            'total' => $positions->count(),
        ], 200);
    }

    /**
     * Get all available levels for dropdown/selection
     * Useful for mobile to populate level selector without knowing numeric values
     */
    public function getLevels()
    {
        $levels = [
            [
                'id' => 1,
                'value' => 'director',
                'label' => 'Director',
                'description' => 'Direktur / Kepala',
            ],
            [
                'id' => 2,
                'value' => 'manager',
                'label' => 'Manager',
                'description' => 'Manajer / Pemimpin Tim',
            ],
            [
                'id' => 3,
                'value' => 'staff',
                'label' => 'Staff',
                'description' => 'Staf / Karyawan',
            ],
        ];

        return response()->json([
            'message' => 'Data level berhasil diambil',
            'data' => $levels,
            'total' => count($levels),
        ], 200);
    }

    /**
     * Get positions by level
     */
    public function getByLevel($level)
    {
        // Validate level (1, 2, or 3)
        if (!in_array($level, [1, 2, 3])) {
            return response()->json([
                'message' => 'Level harus 1, 2, atau 3',
            ], 400);
        }

        $positions = Position::where('level', $level)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($positions->isEmpty()) {
            return response()->json([
                'message' => "Posisi dengan level $level tidak ditemukan",
                'data' => [],
            ], 404);
        }

        $levelName = match ($level) {
            1 => 'Director',
            2 => 'Manager',
            3 => 'Staff',
        };

        return response()->json([
            'message' => "Data posisi level $level ($levelName) berhasil diambil",
            'data' => $positions,
            'total' => $positions->count(),
        ], 200);
    }

    /**
     * Get positions user can create (based on their level)
     */
    public function getCreatablePositions(Request $request)
    {
        $authUser = $request->user();
        $userLevel = $authUser->getLevel();

        // Director (level 1) bisa create manager dan staff (level 2 dan 3)
        // Manager (level 2) bisa create staff saja (level 3)
        // Staff (level 3) tidak bisa create user

        $creatableLevel = match ($userLevel) {
            1 => [2, 3],  // Director bisa create level 2 dan 3
            2 => [3],     // Manager bisa create level 3 saja
            default => [], // Staff tidak bisa create
        };

        if (empty($creatableLevel)) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk membuat user',
                'data' => [],
            ], 403);
        }

        $positions = Position::whereIn('level', $creatableLevel)
            ->where('is_active', true)
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        return response()->json([
            'message' => 'Data posisi yang bisa di-create berhasil diambil',
            'data' => $positions,
            'total' => $positions->count(),
            'your_level' => $userLevel,
            'creatable_levels' => $creatableLevel,
        ], 200);
    }

    /**
     * Create new position (Director only)
     * Simplified: Only requires name and level
     * Code auto-generated from name
     *
     * Level dapat dikirim sebagai:
     * - String: "director", "manager", "staff"
     * - Angka: 1, 2, 3
     */
    public function store(Request $request)
    {
        // Check authorization - hanya director
        $authUser = $request->user();
        if (!$authUser || !$authUser->isDirector()) {
            return response()->json([
                'message' => 'Hanya Direktur yang dapat membuat posisi baru',
            ], 403);
        }

        // Validate input - simplified
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
            'level' => 'required|string',
        ]);

        // Map level dari string atau angka ke integer
        $levelMap = [
            '1' => 1,
            'director' => 1,
            '2' => 2,
            'manager' => 2,
            '3' => 3,
            'staff' => 3,
        ];

        $levelInput = strtolower($validated['level']);

        if (!isset($levelMap[$levelInput])) {
            return response()->json([
                'message' => 'Level tidak valid. Gunakan: director/1, manager/2, atau staff/3',
                'valid_levels' => [
                    'director' => 1,
                    'manager' => 2,
                    'staff' => 3,
                ],
            ], 422);
        }

        $level = $levelMap[$levelInput];

        // Auto-generate code dari name
        $code = strtolower(str_replace(' ', '_', $validated['name']));

        // Check if code already exists, append number if needed
        $originalCode = $code;
        $counter = 1;
        while (Position::where('code', $code)->exists()) {
            $code = $originalCode . '_' . $counter;
            $counter++;
        }

        // Create position
        $position = Position::create([
            'name' => $validated['name'],
            'code' => $code,
            'level' => $level,
            'description' => null,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Posisi baru berhasil dibuat',
            'data' => $position,
        ], 201);
    }

    /**
     * Delete/Deactivate position (Director only)
     * Soft delete: Set is_active to false to preserve data
     */
    public function destroy(Request $request, $id)
    {
        // Check authorization - hanya director
        $authUser = $request->user();
        if (!$authUser || !$authUser->isDirector()) {
            return response()->json([
                'message' => 'Hanya Direktur yang dapat menghapus posisi',
            ], 403);
        }

        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'message' => 'Posisi tidak ditemukan',
            ], 404);
        }

        // Check if position has users assigned
        $usersCount = $position->users()->count();
        if ($usersCount > 0) {
            return response()->json([
                'message' => "Tidak dapat menghapus posisi. Masih ada $usersCount user dengan posisi ini",
            ], 422);
        }

        // Soft delete: Set is_active to false
        $position->update(['is_active' => false]);

        return response()->json([
            'message' => 'Posisi berhasil dihapus',
            'data' => $position,
        ], 200);
    }
}
