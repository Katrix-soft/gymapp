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
        Schema::create('general_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('checkin_time');
            $table->date('checkin_date');
            $table->string('method')->default('qr_code'); // qr_code, biometrics, manual
            $table->string('status')->default('active'); // active, expired, pending
            $table->timestamps();
        });

        Schema::create('mercadopago_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_id')->nullable();
            $table->string('topic')->nullable();
            $table->string('resource')->nullable();
            $table->text('payload'); // stored as text for compatibility with both MySQL and SQLite
            $table->string('status')->default('received'); // received, processed, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_checkins');
        Schema::dropIfExists('mercadopago_webhooks');
    }
};
