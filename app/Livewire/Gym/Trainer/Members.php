<?php

namespace App\Livewire\Gym\Trainer;

use App\Models\User;
use App\Models\Membership;
use Livewire\Component;
use Livewire\WithPagination;

class Members extends Component
{
    use WithPagination;

    public $search = '';
    public $showProfileModal = false;
    public $profileMember = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openProfile($memberId)
    {
        $this->profileMember = User::with([
            'memberships.plan',
            'routines',
            'bodyMeasurements' => fn($q) => $q->orderBy('logged_at', 'desc')->limit(5),
            'workoutLogs' => fn($q) => $q->orderBy('completed_at', 'desc')->limit(5),
        ])->findOrFail($memberId);

        $this->showProfileModal = true;
    }

    public function closeProfile()
    {
        $this->showProfileModal = false;
        $this->profileMember = null;
    }

    public function render()
    {
        $members = User::role('member')
            ->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->with(['activeMembership.plan', 'routines'])
            ->orderBy('first_name')
            ->paginate(12);

        return view('livewire.gym.trainer.members', [
            'members' => $members,
        ])->layout('layouts.tenant-app');
    }
}
