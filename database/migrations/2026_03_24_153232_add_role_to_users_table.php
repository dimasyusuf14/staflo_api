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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['director', 'manager', 'staff'])->default('staff')->after('email');
            $table->string('temp_password')->nullable()->after('password');
            $table->timestamp('temp_password_set_at')->nullable()->after('temp_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'temp_password', 'temp_password_set_at']);
        });
    }
};
