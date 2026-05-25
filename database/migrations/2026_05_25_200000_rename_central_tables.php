<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename central tables to avoid conflicts with tenant tables
     * that live in the same shared database.
     */
    public function up(): void
    {
        // Rename central tables to avoid conflict with tenant tables
        if (Schema::hasTable('users') && !Schema::hasTable('central_users')) {
            Schema::rename('users', 'central_users');
        }

        if (Schema::hasTable('password_reset_tokens') && !Schema::hasTable('central_password_reset_tokens')) {
            Schema::rename('password_reset_tokens', 'central_password_reset_tokens');
        }

        if (Schema::hasTable('sessions') && !Schema::hasTable('central_sessions')) {
            Schema::rename('sessions', 'central_sessions');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('central_users') && !Schema::hasTable('users')) {
            Schema::rename('central_users', 'users');
        }

        if (Schema::hasTable('central_password_reset_tokens') && !Schema::hasTable('password_reset_tokens')) {
            Schema::rename('central_password_reset_tokens', 'password_reset_tokens');
        }

        if (Schema::hasTable('central_sessions') && !Schema::hasTable('sessions')) {
            Schema::rename('central_sessions', 'sessions');
        }
    }
};
