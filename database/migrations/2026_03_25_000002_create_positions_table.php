<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create positions table
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Mobile Developer, Web Developer, etc
            $table->string('code')->unique(); // mobile_dev, web_dev, qa, marketing, etc
            $table->integer('level')->comment('1 = Director, 2 = Manager, 3 = Staff');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add position_id column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('set null');
            // Keep 'role' column for backward compatibility, but it will be based on position level
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['position_id']);
            $table->dropColumn('position_id');
        });

        Schema::dropIfExists('positions');
    }
};
