<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BucketController extends Controller
{
    /**
     * Get all active buckets
     */
    public function index()
    {
        $buckets = Bucket::where('is_active', true)
            ->with('creator:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Data bucket berhasil diambil',
            'data' => $buckets,
            'total' => $buckets->count(),
        ], 200);
    }

    /**
     * Create new bucket (Director & Manager only - Level 1 & 2)
     */
    public function store(Request $request)
    {
        $authUser = $request->user();
        $userLevel = $authUser->getLevel();

        // Hanya level 1 (Director) dan level 2 (Manager) bisa create bucket
        if (!in_array($userLevel, [1, 2])) {
            throw ValidationException::withMessages([
                'authorization' => ['Hanya Direktur dan Manager yang dapat membuat bucket.'],
            ]);
        }

        // Validate input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:buckets,name',
            'description' => 'nullable|string|max:500',
        ]);

        // Create bucket
        $bucket = Bucket::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => $authUser->id,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Bucket berhasil dibuat',
            'data' => $bucket->load('creator:id,name'),
        ], 201);
    }

    /**
     * Delete/Deactivate bucket (Director & Manager only - Level 1 & 2)
     */
    public function destroy(Request $request, $id)
    {
        $authUser = $request->user();
        $userLevel = $authUser->getLevel();

        // Hanya level 1 (Director) dan level 2 (Manager) bisa delete bucket
        if (!in_array($userLevel, [1, 2])) {
            throw ValidationException::withMessages([
                'authorization' => ['Hanya Direktur dan Manager yang dapat menghapus bucket.'],
            ]);
        }

        $bucket = Bucket::find($id);

        if (!$bucket) {
            return response()->json([
                'message' => 'Bucket tidak ditemukan',
            ], 404);
        }

        // Check if bucket has tasks
        $tasksCount = $bucket->tasks()->count();
        if ($tasksCount > 0) {
            return response()->json([
                'message' => "Tidak dapat menghapus bucket. Masih ada $tasksCount task di bucket ini",
            ], 422);
        }

        // Soft delete: Set is_active to false
        $bucket->update(['is_active' => false]);

        return response()->json([
            'message' => 'Bucket berhasil dihapus',
            'data' => $bucket,
        ], 200);
    }
}
