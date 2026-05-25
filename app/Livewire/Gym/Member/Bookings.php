<?php

namespace App\Livewire\Gym\Member;

use App\Models\GymClass;
use App\Models\ClassBooking;
use Livewire\Component;
use Carbon\Carbon;

class Bookings extends Component
{
    // Filter by day of week
    public $filterDay = '';
    
    // Booking target date (defaults to today)
    public $bookingDate = '';

    public $weekdays = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        0 => 'Domingo',
    ];

    public function mount()
    {
        $this->bookingDate = now()->toDateString();
        // Set filterDay based on target date day of week
        $this->filterDay = (string) Carbon::parse($this->bookingDate)->dayOfWeek;
    }

    public function updatedBookingDate($value)
    {
        if ($value) {
            $this->filterDay = (string) Carbon::parse($value)->dayOfWeek;
        }
    }

    public function bookClass($classId)
    {
        $this->validate([
            'bookingDate' => 'required|date|after_or_equal:today',
        ]);

        $gymClass = GymClass::findOrFail($classId);
        $user = auth()->user();

        // 1. Verify capacity
        $currentBookingsCount = ClassBooking::where('gym_class_id', $classId)
            ->where('date', $this->bookingDate)
            ->count();

        if ($currentBookingsCount >= $gymClass->capacity) {
            session()->flash('error', 'Esta clase ya está llena para la fecha seleccionada.');
            return;
        }

        // 2. Verify unique reservation
        $exists = ClassBooking::where('user_id', $user->id)
            ->where('gym_class_id', $classId)
            ->where('date', $this->bookingDate)
            ->exists();

        if ($exists) {
            session()->flash('error', 'Ya has reservado un cupo para esta clase.');
            return;
        }

        // 3. Create booking
        ClassBooking::create([
            'user_id' => $user->id,
            'gym_class_id' => $classId,
            'date' => $this->bookingDate,
            'status' => 'booked',
        ]);

        session()->flash('message', '¡Cupo reservado con éxito!');
    }

    public function cancelBooking($classId)
    {
        $this->validate([
            'bookingDate' => 'required|date',
        ]);

        $user = auth()->user();

        ClassBooking::where('user_id', $user->id)
            ->where('gym_class_id', $classId)
            ->where('date', $this->bookingDate)
            ->delete();

        session()->flash('message', 'Reserva cancelada con éxito.');
    }

    public function render()
    {
        $user = auth()->user();

        // Get classes matching selected day of week
        $classes = GymClass::with('trainer')
            ->where('day_of_week', $this->filterDay)
            ->orderBy('start_time', 'asc')
            ->get();

        // Build data structure checking booking states
        $classList = [];
        foreach ($classes as $c) {
            $bookedCount = ClassBooking::where('gym_class_id', $c->id)
                ->where('date', $this->bookingDate)
                ->count();

            $isBooked = ClassBooking::where('user_id', $user->id)
                ->where('gym_class_id', $c->id)
                ->where('date', $this->bookingDate)
                ->exists();

            $classList[] = [
                'id' => $c->id,
                'name' => $c->name,
                'description' => $c->description,
                'start_time' => substr($c->start_time, 0, 5),
                'end_time' => substr($c->end_time, 0, 5),
                'capacity' => $c->capacity,
                'trainer_name' => $c->trainer->name ?? 'Instructor',
                'booked_count' => $bookedCount,
                'is_booked' => $isBooked,
            ];
        }

        return view('livewire.gym.member.bookings', [
            'classList' => $classList,
        ])->layout('layouts.tenant-app');
    }
}
