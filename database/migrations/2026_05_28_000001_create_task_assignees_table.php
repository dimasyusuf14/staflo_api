<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['task_id', 'user_id']);
        });

        // Migrate existing assigned_to data to pivot table
        DB::table('tasks')
            ->whereNotNull('assigned_to')
            ->get()
            ->each(function ($task) {
                DB::table('task_assignees')->insert([
                    'task_id'    => $task->id,
                    'user_id'    => $task->assigned_to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        // Drop assigned_to column from tasks
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        // Restore: use first assignee per task
        DB::table('task_assignees')
            ->select('task_id', DB::raw('MIN(user_id) as user_id'))
            ->groupBy('task_id')
            ->get()
            ->each(function ($row) {
                DB::table('tasks')
                    ->where('id', $row->task_id)
                    ->update(['assigned_to' => $row->user_id]);
            });

        Schema::dropIfExists('task_assignees');
    }
};
