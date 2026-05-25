<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CentralUser;
use App\Models\Tenant;
use App\Models\SaasPlan;
use App\Models\SaasSubscription;
use App\Models\Plan;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\GymClass;
use App\Models\ClassBooking;
use App\Models\AttendanceRecord;
use App\Models\Exercise;
use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\RoutineExercise;
use App\Models\BodyMeasurement;
use App\Models\TenantConfig;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (tenant()) {
            $this->seedTenant();
        } else {
            $this->seedCentral();
        }
    }

    /**
     * Seed the central database.
     */
    protected function seedCentral(): void
    {
        // 1. Create Super Admin User (central users table)
        if (!CentralUser::where('email', 'admin@gym.test')->exists()) {
            CentralUser::create([
                'name' => 'Super Admin',
                'email' => 'admin@gym.test',
                'password' => Hash::make('password'),
            ]);
        }

        // 2. Create SaaS Plans
        if (SaasPlan::count() === 0) {
            $basic = SaasPlan::create([
                'name' => 'Basic',
                'description' => 'Perfect for small gyms. Up to 100 members & 5 trainers.',
                'price' => 49.00,
                'limit_members' => 100,
                'limit_trainers' => 5,
            ]);

            $pro = SaasPlan::create([
                'name' => 'Pro',
                'description' => 'Ideal for growing gyms. Up to 500 members & 15 trainers.',
                'price' => 99.00,
                'limit_members' => 500,
                'limit_trainers' => 15,
            ]);

            $enterprise = SaasPlan::create([
                'name' => 'Enterprise',
                'description' => 'For large fitness clubs. Unlimited members & trainers.',
                'price' => 199.00,
                'limit_members' => 9999,
                'limit_trainers' => 999,
            ]);
        }

        // 3. Create GymDemo Tenant
        if (!Tenant::where('id', 'gymdemo')->exists()) {
            $tenant = Tenant::create([
                'id' => 'gymdemo',
                'name' => 'GymDemo',
                'owner_email' => 'admin@gymdemo.com',
            ]);

            // Create Domain for GymDemo
            $tenant->domains()->create([
                'domain' => 'gymdemo.gym.test',
            ]);

            // Create SaaS Subscription
            $proPlan = SaasPlan::where('name', 'Pro')->first();
            SaasSubscription::create([
                'tenant_id' => 'gymdemo',
                'saas_plan_id' => $proPlan->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'status' => 'active',
            ]);
        }
    }

    /**
     * Seed the tenant database.
     */
    protected function seedTenant(): void
    {
        // 1. Seed Tenant configs (Branding & Credentials)
        TenantConfig::set('gym_name', 'GymDemo Center');
        TenantConfig::set('logo_url', 'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?q=80&w=200&auto=format&fit=crop');
        TenantConfig::set('brand_color', '#FF6B35');
        TenantConfig::set('cancellation_policy_hours', '2');
        
        // Mercado Pago credentials (from credenciales usar.txt)
        TenantConfig::set('mp_client_id', '8359143020343721');
        TenantConfig::set('mp_client_secret', 'QteVdMtegnrexpqONZhRnjzGcYh0fvK4');
        TenantConfig::set('mp_access_token', 'APP_USR-8359143020343721-040615-f59882fd778f2c250e4fc46db7b7b2bb-359122329');
        TenantConfig::set('mp_public_key', 'APP_USR-718e7378-addf-48f1-b69e-c822360053e3');

        // 2. Roles
        $adminRole = Role::firstOrCreate(['name' => 'gym_admin', 'guard_name' => 'web']);
        $trainerRole = Role::firstOrCreate(['name' => 'trainer', 'guard_name' => 'web']);
        $memberRole = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

        // 3. Gym Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@gymdemo.com'],
            [
                'first_name' => 'Franco',
                'last_name' => 'Admin',
                'password' => Hash::make('password'),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150',
                'gym_code' => 'DEMO100',
                'status' => 'active',
            ]
        );
        if (!$admin->hasRole('gym_admin')) {
            $admin->assignRole($adminRole);
        }

        // 4. Trainer User
        $trainer = User::firstOrCreate(
            ['email' => 'trainer@gymdemo.com'],
            [
                'first_name' => 'Carlos',
                'last_name' => 'Entrenador',
                'password' => Hash::make('password'),
                'profile_photo_url' => 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?w=150',
                'gym_code' => 'DEMO200',
                'status' => 'active',
            ]
        );
        if (!$trainer->hasRole('trainer')) {
            $trainer->assignRole($trainerRole);
        }

        // 5. Member Users (5 members)
        $members = [];
        $names = [
            ['Juan', 'Pérez', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150'],
            ['María', 'Gómez', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150'],
            ['Lucas', 'Martínez', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'],
            ['Ana', 'Rodríguez', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150'],
            ['Diego', 'Sánchez', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150'],
        ];

        foreach ($names as $index => $nameData) {
            $i = $index + 1;
            $member = User::firstOrCreate(
                ['email' => "member{$i}@gymdemo.com"],
                [
                    'first_name' => $nameData[0],
                    'last_name' => $nameData[1],
                    'password' => Hash::make('password'),
                    'profile_photo_url' => $nameData[2],
                    'gym_code' => 'DEMO30' . $i,
                    'status' => 'active',
                ]
            );
            if (!$member->hasRole('member')) {
                $member->assignRole($memberRole);
            }
            $members[] = $member;
        }

        // 6. Membership Plans
        $planMensual = Plan::firstOrCreate(
            ['name' => 'Plan Mensual'],
            ['description' => 'Acceso libre a sala de musculación por 1 mes.', 'price' => 15000.00, 'duration_months' => 1]
        );

        $planTrimestral = Plan::firstOrCreate(
            ['name' => 'Plan Trimestral'],
            ['description' => 'Paga 3 meses con descuento. Incluye clases grupales.', 'price' => 40000.00, 'duration_months' => 3]
        );

        $planAnual = Plan::firstOrCreate(
            ['name' => 'Plan Anual'],
            ['description' => 'El mejor precio. Incluye pase libre a todo e invitaciones.', 'price' => 140000.00, 'duration_months' => 12]
        );

        // 7. Assign Memberships & Payments
        // Approved Payments & Active Memberships for members 1 to 4
        // Pending Payment & Membership for member 5
        $plansList = [$planMensual, $planTrimestral, $planAnual, $planMensual, $planTrimestral];
        foreach ($members as $index => $member) {
            $planSelected = $plansList[$index];
            $isPending = ($index === 4);

            $startDate = $isPending ? now()->addDay() : now()->subDays(10);
            $endDate = $startDate->copy()->addMonths($planSelected->duration_months);

            $membership = Membership::firstOrCreate(
                ['user_id' => $member->id, 'plan_id' => $planSelected->id],
                [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'status' => $isPending ? 'pending' : 'active',
                ]
            );

            Payment::firstOrCreate(
                ['membership_id' => $membership->id],
                [
                    'user_id' => $member->id,
                    'amount' => $planSelected->price,
                    'status' => $isPending ? 'pending' : 'approved',
                    'external_reference' => 'mp_' . uniqid(),
                    'payment_method' => $isPending ? null : 'credit_card',
                    'pdf_path' => $isPending ? null : 'receipts/receipt_' . $membership->id . '.pdf',
                ]
            );
        }

        // 8. Exercises (10 Real Exercises)
        $exercisesData = [
            ['Prensa de Pecho (Bench Press)', 'Chest', 'Acuéstate en el banco, baja la barra al pecho y empuja hacia arriba.', 'https://www.youtube.com/embed/rT7DgCrgWys'],
            ['Sentadillas Libres (Squats)', 'Legs', 'Coloca la barra en los trapecios, baja la cadera manteniendo la espalda recta.', 'https://www.youtube.com/embed/U3HlEF_E9fo'],
            ['Dominadas (Pull-ups)', 'Back', 'Cuélgate de la barra y elévate hasta pasar la barbilla.', 'https://www.youtube.com/embed/eGo4IYlbE5g'],
            ['Peso Muerto (Deadlift)', 'Back', 'Levanta la barra desde el suelo manteniendo la espalda neutra y activando glúteos.', 'https://www.youtube.com/embed/op9kVnSso6Q'],
            ['Prensa Militar (Overhead Press)', 'Shoulders', 'Empuja la barra verticalmente por encima de la cabeza.', 'https://www.youtube.com/embed/2yjwXTZQDDI'],
            ['Curl de Bíceps con Barra', 'Arms', 'Sujeta la barra con agarre supino y flexiona los codos sin mover los hombros.', 'https://www.youtube.com/embed/ykJgrb5Y0T8'],
            ['Tríceps en Polea Alta', 'Arms', 'Empuja la barra de la polea hacia abajo extendiendo completamente los brazos.', 'https://www.youtube.com/embed/2-LAMgA9yOw'],
            ['Prensa de Piernas (Leg Press)', 'Legs', 'Empuja la plataforma con las piernas sin bloquear las rodillas.', 'https://www.youtube.com/embed/IZxyjW7MPJQ'],
            ['Vuelos Laterales (Lateral Raise)', 'Shoulders', 'Eleva las mancuernas lateralmente hasta la altura de los hombros.', 'https://www.youtube.com/embed/3VcKaXtokdU'],
            ['Plancha Abdominal (Plank)', 'Core', 'Mantén el cuerpo alineado apoyándote sobre antebrazos y puntas de pies.', 'https://www.youtube.com/embed/pSHjTRCQxIw'],
        ];

        $exercises = [];
        foreach ($exercisesData as $ex) {
            $exercises[] = Exercise::firstOrCreate(
                ['name' => $ex[0]],
                ['muscle_group' => $ex[1], 'instructions' => $ex[2], 'video_url' => $ex[3]]
            );
        }

        // 9. Routines
        // Routine 1: Strength (Fuerza) 3 Days for Member 1
        $member1 = $members[0];
        $routineStrength = Routine::firstOrCreate(
            ['member_id' => $member1->id, 'name' => 'Rutina de Fuerza - 3 Días'],
            [
                'description' => 'Rutina de hipertrofia y fuerza general, dividida en Empuje/Tirón/Pierna.',
                'trainer_id' => $trainer->id,
            ]
        );

        // Day 1: Empuje
        $dayPush = RoutineDay::firstOrCreate(['routine_id' => $routineStrength->id, 'name' => 'Día 1: Empuje (Pecho/Hombro/Tríceps)']);
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayPush->id, 'exercise_id' => $exercises[0]->id], // Bench Press
            ['sets' => 4, 'reps' => '8-10', 'weight' => 60.00, 'rest_seconds' => 90, 'sort_order' => 1]
        );
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayPush->id, 'exercise_id' => $exercises[4]->id], // Overhead Press
            ['sets' => 4, 'reps' => '8-10', 'weight' => 30.00, 'rest_seconds' => 90, 'sort_order' => 2]
        );
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayPush->id, 'exercise_id' => $exercises[6]->id], // Tricep Polea
            ['sets' => 3, 'reps' => '12', 'weight' => 20.00, 'rest_seconds' => 60, 'sort_order' => 3]
        );

        // Day 2: Tirón
        $dayPull = RoutineDay::firstOrCreate(['routine_id' => $routineStrength->id, 'name' => 'Día 2: Tirón (Espalda/Bíceps)']);
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayPull->id, 'exercise_id' => $exercises[2]->id], // Pull-ups
            ['sets' => 4, 'reps' => 'Fallo', 'weight' => 0.00, 'rest_seconds' => 90, 'sort_order' => 1]
        );
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayPull->id, 'exercise_id' => $exercises[3]->id], // Deadlift
            ['sets' => 3, 'reps' => '6', 'weight' => 80.00, 'rest_seconds' => 120, 'sort_order' => 2]
        );
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayPull->id, 'exercise_id' => $exercises[5]->id], // Curl Bíceps
            ['sets' => 3, 'reps' => '12', 'weight' => 25.00, 'rest_seconds' => 60, 'sort_order' => 3]
        );

        // Day 3: Pierna
        $dayLegs = RoutineDay::firstOrCreate(['routine_id' => $routineStrength->id, 'name' => 'Día 3: Piernas']);
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayLegs->id, 'exercise_id' => $exercises[1]->id], // Squats
            ['sets' => 4, 'reps' => '8-10', 'weight' => 70.00, 'rest_seconds' => 120, 'sort_order' => 1]
        );
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayLegs->id, 'exercise_id' => $exercises[7]->id], // Leg Press
            ['sets' => 3, 'reps' => '10-12', 'weight' => 120.00, 'rest_seconds' => 90, 'sort_order' => 2]
        );

        // Routine 2: Cardio & Core for Member 2
        $member2 = $members[1];
        $routineCardio = Routine::firstOrCreate(
            ['member_id' => $member2->id, 'name' => 'Acondicionamiento Cardio y Abdomen'],
            [
                'description' => 'Rutina enfocada en pérdida de grasa y fortalecimiento del core.',
                'trainer_id' => $trainer->id,
            ]
        );
        $dayCardio1 = RoutineDay::firstOrCreate(['routine_id' => $routineCardio->id, 'name' => 'Día 1: HIIT']);
        RoutineExercise::firstOrCreate(
            ['routine_day_id' => $dayCardio1->id, 'exercise_id' => $exercises[9]->id], // Plank
            ['sets' => 4, 'reps' => '60s', 'weight' => 0.00, 'rest_seconds' => 45, 'sort_order' => 1]
        );

        // 10. Weekly Classes (Monday to Friday, 3 classes per day)
        // Days of week: 1 = Mon, 2 = Tue, 3 = Wed, 4 = Thu, 5 = Fri
        $classNames = [
            ['Morning Yoga', '08:00:00', '09:00:00', 15],
            ['Midday Crossfit', '13:00:00', '14:00:00', 20],
            ['Evening Spinning', '19:30:00', '20:30:00', 25],
        ];

        for ($day = 1; $day <= 5; $day++) {
            foreach ($classNames as $classData) {
                $gc = GymClass::firstOrCreate(
                    [
                        'day_of_week' => $day,
                        'start_time' => $classData[1],
                        'name' => $classData[0],
                    ],
                    [
                        'description' => 'Clase de ' . $classData[0] . ' para todos los niveles.',
                        'end_time' => $classData[2],
                        'capacity' => $classData[3],
                        'trainer_id' => $trainer->id,
                    ]
                );

                // Book Member 1 and Member 2 to some classes
                if ($day === 1 && $classData[0] === 'Midday Crossfit') { // Monday Crossfit
                    ClassBooking::firstOrCreate([
                        'user_id' => $member1->id,
                        'gym_class_id' => $gc->id,
                        'date' => Carbon::now()->startOfWeek()->toDateString(), // Monday
                    ]);
                    ClassBooking::firstOrCreate([
                        'user_id' => $member2->id,
                        'gym_class_id' => $gc->id,
                        'date' => Carbon::now()->startOfWeek()->toDateString(), // Monday
                    ]);

                    // Seed some attendance records
                    AttendanceRecord::firstOrCreate([
                        'user_id' => $member1->id,
                        'gym_class_id' => $gc->id,
                        'date' => Carbon::now()->startOfWeek()->toDateString(),
                    ], ['status' => 'present']);

                    AttendanceRecord::firstOrCreate([
                        'user_id' => $member2->id,
                        'gym_class_id' => $gc->id,
                        'date' => Carbon::now()->startOfWeek()->toDateString(),
                    ], ['status' => 'absent']);
                }
            }
        }

        // 11. Body Measurements (last 3 months for Member 1)
        for ($month = 3; $month >= 1; $month--) {
            BodyMeasurement::firstOrCreate(
                [
                    'user_id' => $member1->id,
                    'logged_at' => Carbon::now()->subMonths($month)->toDateString(),
                ],
                [
                    'weight' => 82.5 - ($month * 0.8), // weight goes down slightly
                    'height' => 1.80,
                    'chest' => 102.0 + ($month * 0.5),
                    'waist' => 88.0 - ($month * 0.6),
                    'fat_percentage' => 18.5 - ($month * 0.4),
                ]
            );
        }
    }
}
