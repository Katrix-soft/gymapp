<?php

namespace App\Livewire\Gym\Admin;

use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\RoutineExercise;
use App\Models\User;
use App\Models\Exercise;
use Livewire\Component;
use Livewire\WithPagination;

class Routines extends Component
{
    use WithPagination;

    public $search = '';

    // Create/Edit Workspace
    public $showCreateModal = false;
    public $editingRoutineId = null;

    // Core Routine properties
    public $routineName = '';
    public $routineDescription = '';
    public $selectedMemberId = '';
    public $memberSearch = '';

    // Interactive Day Builder state
    // Array format: [ ['name' => 'Día 1', 'exercises' => [ ['exercise_id' => '', 'sets' => 4, 'reps' => '12', 'weight' => 20, 'rest_seconds' => 60, 'notes' => ''] ] ] ]
    public $days = [];

    // Add New Exercise Modal helper
    public $showAddExerciseModal = false;
    public $newExName = '';
    public $newExMuscle = 'Pecho';
    public $newExInstructions = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->editingRoutineId = null;
        $this->routineName = '';
        $this->routineDescription = '';
        $this->selectedMemberId = '';
        $this->memberSearch = '';
        
        // Start with 1 default day and 1 exercise template
        $this->days = [
            [
                'name' => 'Día 1: General',
                'exercises' => [
                    [
                        'exercise_id' => '',
                        'sets' => 4,
                        'reps' => '12',
                        'weight' => 20,
                        'rest_seconds' => 60,
                        'notes' => ''
                    ]
                ]
            ]
        ];

        $this->showCreateModal = true;
    }

    public function openEditModal($routineId)
    {
        $this->resetValidation();
        $this->editingRoutineId = $routineId;

        $routine = Routine::with('days.routineExercises')->findOrFail($routineId);
        $this->routineName = $routine->name;
        $this->routineDescription = $routine->description;
        $this->selectedMemberId = $routine->member_id;
        $this->memberSearch = $routine->member->name ?? '';

        $this->days = [];
        foreach ($routine->days as $day) {
            $exs = [];
            foreach ($day->routineExercises as $re) {
                $exs[] = [
                    'exercise_id' => $re->exercise_id,
                    'sets' => $re->sets,
                    'reps' => $re->reps,
                    'weight' => $re->weight,
                    'rest_seconds' => $re->rest_seconds,
                    'notes' => $re->notes
                ];
            }
            $this->days[] = [
                'name' => $day->name,
                'exercises' => !empty($exs) ? $exs : [[
                    'exercise_id' => '',
                    'sets' => 4,
                    'reps' => '12',
                    'weight' => 20,
                    'rest_seconds' => 60,
                    'notes' => ''
                ]]
            ];
        }

        if (empty($this->days)) {
            $this->days = [[
                'name' => 'Día 1',
                'exercises' => [['exercise_id' => '', 'sets' => 4, 'reps' => '12', 'weight' => 20, 'rest_seconds' => 60, 'notes' => '']]
            ]];
        }

        $this->showCreateModal = true;
    }

    // Autocomplete Select member
    public function selectMember($memberId, $memberName)
    {
        $this->selectedMemberId = $memberId;
        $this->memberSearch = $memberName;
    }

    // Add/Remove day in builder
    public function addDay()
    {
        $dayIndex = count($this->days) + 1;
        $this->days[] = [
            'name' => "Día {$dayIndex}: Nuevo",
            'exercises' => [
                [
                    'exercise_id' => '',
                    'sets' => 4,
                    'reps' => '12',
                    'weight' => 20,
                    'rest_seconds' => 60,
                    'notes' => ''
                ]
            ]
        ];
    }

    public function removeDay($dayIndex)
    {
        unset($this->days[$dayIndex]);
        $this->days = array_values($this->days);
    }

    // Add/Remove exercises in builder day
    public function addExerciseToDay($dayIndex)
    {
        $this->days[$dayIndex]['exercises'][] = [
            'exercise_id' => '',
            'sets' => 4,
            'reps' => '12',
            'weight' => 20,
            'rest_seconds' => 60,
            'notes' => ''
        ];
    }

    public function removeExerciseFromDay($dayIndex, $exIndex)
    {
        unset($this->days[$dayIndex]['exercises'][$exIndex]);
        $this->days[$dayIndex]['exercises'] = array_values($this->days[$dayIndex]['exercises']);
    }

    // Save Routine
    public function saveRoutine()
    {
        $this->validate([
            'routineName' => 'required|string|max:255',
            'routineDescription' => 'nullable|string',
            'selectedMemberId' => 'required|exists:users,id',
            'days.*.name' => 'required|string|max:255',
            'days.*.exercises.*.exercise_id' => 'required|exists:exercises,id',
            'days.*.exercises.*.sets' => 'required|integer|min:1',
            'days.*.exercises.*.reps' => 'required|string',
            'days.*.exercises.*.weight' => 'required|numeric|min:0',
            'days.*.exercises.*.rest_seconds' => 'required|integer|min:0',
        ]);

        if ($this->editingRoutineId) {
            $routine = Routine::findOrFail($this->editingRoutineId);
            $routine->update([
                'name' => $this->routineName,
                'description' => $this->routineDescription,
                'member_id' => $this->selectedMemberId,
            ]);

            // Clear old days (cascade deletes routine exercises)
            $routine->days()->delete();
        } else {
            $routine = Routine::create([
                'name' => $this->routineName,
                'description' => $this->routineDescription,
                'member_id' => $this->selectedMemberId,
                'trainer_id' => auth()->id() ?? User::role('gym_admin')->first()->id,
            ]);
        }

        // Insert new days & exercises
        foreach ($this->days as $dIndex => $dData) {
            $day = RoutineDay::create([
                'routine_id' => $routine->id,
                'name' => $dData['name']
            ]);

            foreach ($dData['exercises'] as $exIndex => $exData) {
                RoutineExercise::create([
                    'routine_day_id' => $day->id,
                    'exercise_id' => $exData['exercise_id'],
                    'sets' => $exData['sets'],
                    'reps' => $exData['reps'],
                    'weight' => $exData['weight'],
                    'rest_seconds' => $exData['rest_seconds'],
                    'notes' => $exData['notes'] ?? '',
                    'sort_order' => $exIndex
                ]);
            }
        }

        session()->flash('message', $this->editingRoutineId ? 'Plan de rutina actualizado.' : 'Plan de rutina creado.');
        $this->showCreateModal = false;
    }

    public function deleteRoutine($routineId)
    {
        $routine = Routine::findOrFail($routineId);
        $routine->delete();
        session()->flash('message', 'Plan de rutina eliminado.');
    }

    // Fast Register New Exercise database record
    public function openAddExerciseModal()
    {
        $this->newExName = '';
        $this->newExMuscle = 'Pecho';
        $this->newExInstructions = '';
        $this->showAddExerciseModal = true;
    }

    public function createExercise()
    {
        $this->validate([
            'newExName' => 'required|string|max:255|unique:exercises,name',
            'newExMuscle' => 'required|string|max:100',
            'newExInstructions' => 'nullable|string',
        ]);

        Exercise::create([
            'name' => $this->newExName,
            'muscle_group' => $this->newExMuscle,
            'instructions' => $this->newExInstructions
        ]);

        $this->showAddExerciseModal = false;
        session()->flash('message', 'Ejercicio añadido a la base de datos.');
    }

    public function render()
    {
        // 1. Paginated Routines list
        $routines = Routine::with(['member', 'days.routineExercises.exercise'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhereHas('member', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // 2. Fetch all exercise templates
        $exercises = Exercise::orderBy('name', 'asc')->get();

        // 3. Autocomplete member search
        $searchMembers = [];
        if (strlen($this->memberSearch) >= 2 && !$this->selectedMemberId) {
            $searchMembers = User::role('member')
                ->where(function($q) {
                    $q->where('first_name', 'like', '%' . $this->memberSearch . '%')
                      ->orWhere('last_name', 'like', '%' . $this->memberSearch . '%');
                })
                ->limit(5)
                ->get();
        }

        return view('livewire.gym.admin.routines', [
            'routines' => $routines,
            'exercises' => $exercises,
            'searchMembers' => $searchMembers,
        ])->layout('layouts.tenant-app');
    }
}
