<?php

namespace App\Livewire\Gym\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Members extends Component
{
    use WithPagination;

    // Search and Sort
    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Bulk selection
    public $selectedMembers = [];
    public $selectAll = false;

    // Modal toggles
    public $showCreateEditModal = false;
    public $showProfileModal = false;
    
    // Edit Form State
    public $editingMemberId = null;
    public $first_name = '';
    public $last_name = '';
    public $email = '';
    public $password = '';
    public $status = 'active';
    public $gym_code = '';

    // Selected member for profile detail view
    public $profileMember = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedMembers = User::role('member')
                ->where(function($query) {
                    $query->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedMembers = [];
        }
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Modal openings
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingMemberId = null;
        $this->showCreateEditModal = true;
    }

    public function openEditModal($memberId)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingMemberId = $memberId;
        
        $member = User::findOrFail($memberId);
        $this->first_name = $member->first_name;
        $this->last_name = $member->last_name;
        $this->email = $member->email;
        $this->status = $member->status;
        $this->gym_code = $member->gym_code;
        
        $this->showCreateEditModal = true;
    }

    public function openProfileModal($memberId)
    {
        $this->profileMember = User::with([
            'memberships.plan',
            'payments' => fn($q) => $q->orderBy('created_at', 'desc'),
            'attendanceRecords' => fn($q) => $q->orderBy('date', 'desc')->limit(10),
            'bodyMeasurements' => fn($q) => $q->orderBy('created_at', 'desc'),
            'workoutLogs.routine'
        ])->findOrFail($memberId);

        $this->showProfileModal = true;
    }

    public function saveMember()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                $this->editingMemberId 
                    ? Rule::unique('users')->ignore($this->editingMemberId)
                    : 'unique:users'
            ],
            'status' => 'required|in:active,inactive,suspended',
            'gym_code' => 'nullable|string|max:50',
        ];

        if (!$this->editingMemberId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $validated = $this->validate($rules);

        if ($this->editingMemberId) {
            $member = User::findOrFail($this->editingMemberId);
            $member->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'status' => $this->status,
                'gym_code' => $this->gym_code,
            ]);

            if ($this->password) {
                $member->update(['password' => Hash::make($this->password)]);
            }

            session()->flash('message', 'Socio actualizado exitosamente.');
        } else {
            $member = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'status' => $this->status,
                'gym_code' => $this->gym_code,
            ]);

            $member->assignRole('member');

            session()->flash('message', 'Socio creado exitosamente.');
        }

        $this->showCreateEditModal = false;
        $this->resetForm();
    }

    public function deleteMember($memberId)
    {
        $member = User::findOrFail($memberId);
        $member->delete();
        session()->flash('message', 'Socio eliminado exitosamente.');
    }

    // Bulk Actions
    public function bulkDelete()
    {
        if (empty($this->selectedMembers)) {
            return;
        }

        User::whereIn('id', $this->selectedMembers)->delete();
        $this->selectedMembers = [];
        $this->selectAll = false;
        session()->flash('message', 'Socios seleccionados eliminados exitosamente.');
    }

    public function bulkActivate()
    {
        if (empty($this->selectedMembers)) {
            return;
        }

        User::whereIn('id', $this->selectedMembers)->update(['status' => 'active']);
        $this->selectedMembers = [];
        $this->selectAll = false;
        session()->flash('message', 'Socios seleccionados activados exitosamente.');
    }

    public function bulkDeactivate()
    {
        if (empty($this->selectedMembers)) {
            return;
        }

        User::whereIn('id', $this->selectedMembers)->update(['status' => 'inactive']);
        $this->selectedMembers = [];
        $this->selectAll = false;
        session()->flash('message', 'Socios seleccionados desactivados exitosamente.');
    }

    private function resetForm()
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->password = '';
        $this->status = 'active';
        $this->gym_code = '';
        $this->editingMemberId = null;
    }

    public function render()
    {
        $members = User::role('member')
            ->where(function($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('gym_code', 'like', '%' . $this->search . '%');
            })
            ->with(['activeMembership.plan'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.gym.admin.members', [
            'members' => $members,
        ])->layout('layouts.tenant-app');
    }
}
