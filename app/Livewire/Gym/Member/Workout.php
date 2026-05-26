<?php

namespace App\Livewire\Gym\Member;

use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\RoutineExercise;
use App\Models\WorkoutLog;
use App\Models\WorkoutSetLog;
use Livewire\Component;

class Workout extends Component
{
    public $routine = null;
    public $selectedDay = null;
    public $selectedDayId = null;

    // Player states
    public $started = false;
    public $startTime = null;

    // Track user workout input values:
    // Format: $workoutData[$exerciseId][$setIndex] = ['reps' => 12, 'weight' => 20, 'completed' => false]
    public $workoutData = [];

    // Active rest timer duration (seconds)
    public $restTimerSeconds = 60;

    public function mount()
    {
        $this->loadRoutine();
    }

    public function loadRoutine()
    {
        $user = auth()->user();
        $this->routine = Routine::where('member_id', $user->id)
            ->with('days.routineExercises.exercise')
            ->first();

        if ($this->routine && count($this->routine->days) > 0) {
            $this->selectDay($this->routine->days->first()->id);
        }
    }

    public function selectDay($dayId)
    {
        $this->selectedDayId = $dayId;
        $this->selectedDay = RoutineDay::with('routineExercises.exercise')->findOrFail($dayId);
        
        $this->started = false;
        $this->workoutData = [];

        // Initialize set log templates
        foreach ($this->selectedDay->routineExercises as $re) {
            $exerciseId = $re->exercise_id;
            $this->workoutData[$exerciseId] = [];
            
            for ($i = 0; $i < $re->sets; $i++) {
                $this->workoutData[$exerciseId][$i] = [
                    'reps' => $re->reps,
                    'weight' => $re->weight,
                    'completed' => false,
                    'rest_seconds' => $re->rest_seconds,
                ];
            }
        }
    }

    public function startWorkout()
    {
        $this->started = true;
        $this->startTime = now();
        session()->flash('message', '¡Entrenamiento iniciado! Registra tus series a continuación.');
    }

    public function toggleSet($exerciseId, $setIndex)
    {
        if (!$this->started) return;

        $completed = !$this->workoutData[$exerciseId][$setIndex]['completed'];
        $this->workoutData[$exerciseId][$setIndex]['completed'] = $completed;

        // If marked as completed, trigger rest timer
        if ($completed) {
            $rest = $this->workoutData[$exerciseId][$setIndex]['rest_seconds'] ?? 60;
            $this->restTimerSeconds = $rest;
            $this->dispatch('start-rest-timer', seconds: $rest);
        }
    }

    public function completeWorkout()
    {
        if (!$this->started) return;

        $user = auth()->user();

        // 1. Create Workout Log
        $log = WorkoutLog::create([
            'user_id' => $user->id,
            'routine_id' => $this->routine->id,
            'completed_at' => now(),
        ]);

        // 2. Save detailed set logs for completed sets
        foreach ($this->workoutData as $exId => $sets) {
            foreach ($sets as $setIndex => $setData) {
                if ($setData['completed']) {
                    WorkoutSetLog::create([
                        'workout_log_id' => $log->id,
                        'exercise_id' => $exId,
                        'set_number' => $setIndex + 1,
                        'reps' => (int) $setData['reps'],
                        'weight' => (float) $setData['weight'],
                    ]);
                }
            }
        }

        session()->flash('message', '¡Entrenamiento completado exitosamente! Buen trabajo.');
        
        $prefix = request()->segment(1) === 'g' ? '/g/' . tenant('id') : '';
        return redirect($prefix . '/member/dashboard');
    }

    public function render()
    {
        return view('livewire.gym.member.workout')
            ->layout('layouts.tenant-app');
    }
}
