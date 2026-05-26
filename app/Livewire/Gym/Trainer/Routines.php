<?php

namespace App\Livewire\Gym\Trainer;

use App\Models\User;
use App\Models\Routine;
use App\Models\RoutineDay;
use App\Models\RoutineExercise;
use App\Models\Exercise;
use Livewire\Component;

class Routines extends Component
{
    public $search = '';

    // Modal states
    public $showCreateModal = false;
    public $showDetailModal = false;

    // Form fields
    public $editingRoutineId = null;
    public $routineName = '';
    public $routineDescription = '';
    public $memberId = '';

    // Detail view
    public $detailRoutine = null;

    // Add day/exercise modal
    public $showAddDayModal = false;
    public $newDayName = '';
    public $addingRoutineId = null;

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->editingRoutineId = null;
        $this->routineName = '';
        $this->routineDescription = '';
        $this->memberId = '';
        $this->showCreateModal = true;
    }

    public function saveRoutine()
    {
        $this->validate([
            'routineName' => 'required|string|max:255',
            'routineDescription' => 'nullable|string',
            'memberId' => 'required|exists:users,id',
        ]);

        $trainerId = auth()->id();

        if ($this->editingRoutineId) {
            $routine = Routine::findOrFail($this->editingRoutineId);
            $routine->update([
                'name' => $this->routineName,
                'description' => $this->routineDescription,
                'member_id' => $this->memberId,
            ]);
            session()->flash('message', 'Rutina actualizada exitosamente.');
        } else {
            Routine::create([
                'name' => $this->routineName,
                'description' => $this->routineDescription,
                'trainer_id' => $trainerId,
                'member_id' => $this->memberId,
            ]);
            session()->flash('message', 'Rutina creada exitosamente.');
        }

        $this->showCreateModal = false;
    }

    public function openEditModal($routineId)
    {
        $routine = Routine::findOrFail($routineId);
        $this->editingRoutineId = $routineId;
        $this->routineName = $routine->name;
        $this->routineDescription = $routine->description;
        $this->memberId = $routine->member_id;
        $this->showCreateModal = true;
    }

    public function viewRoutineDetail($routineId)
    {
        $this->detailRoutine = Routine::with('days.routineExercises.exercise', 'member')
            ->findOrFail($routineId);
        $this->showDetailModal = true;
    }

    public function deleteRoutine($routineId)
    {
        Routine::findOrFail($routineId)->delete();
        session()->flash('message', 'Rutina eliminada exitosamente.');
    }

    public function openAddDayModal($routineId)
    {
        $this->addingRoutineId = $routineId;
        $this->newDayName = '';
        $this->showAddDayModal = true;
    }

    public function saveDay()
    {
        $this->validate([
            'newDayName' => 'required|string|max:255',
        ]);

        RoutineDay::create([
            'routine_id' => $this->addingRoutineId,
            'name' => $this->newDayName,
        ]);

        $this->showAddDayModal = false;
        session()->flash('message', 'Día de rutina agregado.');

        // Refresh detail if open
        if ($this->detailRoutine && $this->detailRoutine->id === $this->addingRoutineId) {
            $this->viewRoutineDetail($this->addingRoutineId);
        }
    }

    public function render()
    {
        $trainerId = auth()->id();

        $routines = Routine::where('trainer_id', $trainerId)
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->with(['member', 'days.routineExercises'])
            ->orderBy('created_at', 'desc')
            ->get();

        $members = User::role('member')->orderBy('first_name')->get();
        $exercises = Exercise::orderBy('name')->get();

        return view('livewire.gym.trainer.routines', [
            'routines' => $routines,
            'members' => $members,
            'exercises' => $exercises,
        ])->layout('layouts.tenant-app');
    }
}
