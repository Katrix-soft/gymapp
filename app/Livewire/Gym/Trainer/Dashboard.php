<?php

namespace App\Livewire\Gym\Trainer;

use App\Models\User;
use App\Models\GymClass;
use App\Models\ClassBooking;
use App\Models\Routine;
use Livewire\Component;

class Dashboard extends Component
{
    public $search = '';

    public function render()
    {
        $trainerId = auth()->id() ?? User::role('trainer')->first()->id;

        // Taught classes today
        $todayClasses = GymClass::where('trainer_id', $trainerId)
            ->where('day_of_week', now()->dayOfWeek)
            ->orderBy('start_time', 'asc')
            ->get();

        // Total members count
        $membersCount = User::role('member')->count();

        // Total bookings in trainer's classes today
        $bookingsCount = ClassBooking::whereHas('gymClass', function($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            })
            ->where('date', now()->toDateString())
            ->count();

        // Members search log
        $members = User::role('member')
            ->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->with(['memberships', 'routines'])
            ->paginate(8);

        return view('livewire.gym.trainer.dashboard', [
            'todayClasses' => $todayClasses,
            'membersCount' => $membersCount,
            'bookingsCount' => $bookingsCount,
            'members' => $members,
        ])->layout('layouts.tenant-app');
    }
}
