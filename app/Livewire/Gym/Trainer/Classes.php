<?php

namespace App\Livewire\Gym\Trainer;

use App\Models\GymClass;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\ClassBooking;
use Livewire\Component;

class Classes extends Component
{
    public $selectedClassId = null;
    public $attendanceDate = '';
    public $attendanceRecords = [];

    public $weekdays = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        0 => 'Domingo'
    ];

    public function mount()
    {
        $this->attendanceDate = now()->toDateString();
    }

    public function selectClass($classId)
    {
        $this->selectedClassId = $classId;
        $this->loadAttendance();
    }

    public function loadAttendance()
    {
        if (!$this->selectedClassId) {
            $this->attendanceRecords = [];
            return;
        }

        $bookings = ClassBooking::where('gym_class_id', $this->selectedClassId)
            ->where('date', $this->attendanceDate)
            ->with('user')
            ->get();

        $records = AttendanceRecord::where('gym_class_id', $this->selectedClassId)
            ->where('date', $this->attendanceDate)
            ->get()
            ->keyBy('user_id');

        $list = [];
        foreach ($bookings as $booking) {
            $userId = $booking->user_id;
            $list[$userId] = [
                'user_id' => $userId,
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'status' => $records->has($userId) ? $records[$userId]->status : 'absent',
            ];
        }

        foreach ($records as $userId => $record) {
            if (!isset($list[$userId])) {
                $user = User::find($userId);
                $list[$userId] = [
                    'user_id' => $userId,
                    'name' => $user ? $user->name : 'Eliminado',
                    'email' => $user ? $user->email : '',
                    'status' => $record->status,
                ];
            }
        }

        $this->attendanceRecords = array_values($list);
    }

    public function toggleAttendance($userId, $status)
    {
        if (!$this->selectedClassId) return;

        AttendanceRecord::updateOrCreate(
            [
                'gym_class_id' => $this->selectedClassId,
                'user_id' => $userId,
                'date' => $this->attendanceDate,
            ],
            ['status' => $status]
        );

        $this->loadAttendance();
    }

    public function updatedAttendanceDate()
    {
        $this->loadAttendance();
    }

    public function render()
    {
        $trainerId = auth()->id();

        $todayClasses = GymClass::where('trainer_id', $trainerId)
            ->where('day_of_week', now()->dayOfWeek)
            ->orderBy('start_time', 'asc')
            ->get();

        $allClasses = GymClass::where('trainer_id', $trainerId)
            ->orderBy('day_of_week', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $selectedClass = $this->selectedClassId ? GymClass::find($this->selectedClassId) : null;

        return view('livewire.gym.trainer.classes', [
            'todayClasses' => $todayClasses,
            'allClasses' => $allClasses,
            'selectedClass' => $selectedClass,
        ])->layout('layouts.tenant-app');
    }
}
