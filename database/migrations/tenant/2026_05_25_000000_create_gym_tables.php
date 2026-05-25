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
        // 1. Tenant Configs (key-value settings)
        Schema::create('tenant_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 2. Plans (Gym membership plans)
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('duration_months')->default(1);
            $table->timestamps();
        });

        // 3. Memberships (Associates Plan to User)
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->index();
            $table->string('status')->default('pending')->index(); // active, expired, pending, suspended
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'end_date']);
        });

        // 4. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('membership_id')->nullable()->constrained('memberships')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending')->index(); // approved, pending, rejected
            $table->string('external_reference')->nullable()->index(); // MercadoPago preference or transaction ID
            $table->string('payment_method')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('mp_preference_id')->nullable();
            $table->timestamps();
        });

        // 5. Gym Classes
        Schema::create('gym_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('day_of_week'); // 0 = Sunday, 1 = Monday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity')->default(20);
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Class Bookings
        Schema::create('class_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('gym_class_id')->constrained('gym_classes')->onDelete('cascade');
            $table->date('date');
            $table->timestamps();

            $table->unique(['user_id', 'gym_class_id', 'date']);
        });

        // 7. Attendance Records
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('gym_class_id')->constrained('gym_classes')->onDelete('cascade');
            $table->date('date');
            $table->string('status')->default('present'); // present, absent
            $table->timestamps();

            $table->unique(['user_id', 'gym_class_id', 'date']);
        });

        // 8. Exercises
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('muscle_group'); // e.g. Chest, Legs, Back
            $table->text('instructions')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();
        });

        // 9. Routines
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 10. Routine Days
        Schema::create('routine_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_id')->constrained('routines')->onDelete('cascade');
            $table->string('name'); // e.g. Day 1: Chest & Triceps
            $table->timestamps();
        });

        // 11. Routine Exercises
        Schema::create('routine_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_day_id')->constrained('routine_days')->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->integer('sets')->default(4);
            $table->string('reps')->default('12'); // Can be range like "10-12" or "AMRAP"
            $table->decimal('weight', 8, 2)->default(0);
            $table->integer('rest_seconds')->default(60);
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 12. Workout Logs (Member completions)
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('routine_id')->nullable()->constrained('routines')->onDelete('set null');
            $table->timestamp('completed_at');
            $table->timestamps();
        });

        // 13. Workout Set Logs
        Schema::create('workout_set_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_log_id')->constrained('workout_logs')->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->integer('set_number');
            $table->decimal('weight', 8, 2);
            $table->integer('reps');
            $table->boolean('completed')->default(true);
            $table->timestamps();
        });

        // 14. Body Measurements
        Schema::create('body_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('chest', 5, 2)->nullable();
            $table->decimal('waist', 5, 2)->nullable();
            $table->decimal('fat_percentage', 4, 2)->nullable();
            $table->date('logged_at');
            $table->timestamps();
        });

        // 15. Messages (Chat)
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('body_measurements');
        Schema::dropIfExists('workout_set_logs');
        Schema::dropIfExists('workout_logs');
        Schema::dropIfExists('routine_exercises');
        Schema::dropIfExists('routine_days');
        Schema::dropIfExists('routines');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('class_bookings');
        Schema::dropIfExists('gym_classes');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('tenant_configs');
    }
};
