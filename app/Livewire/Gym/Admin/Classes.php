<?php

namespace App\Livewire\Gym\Admin;

use App\Models\GymClass;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\ClassBooking;
use Livewire\Component;
use Livewire\WithPagination;

class Classes extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $filterDay = ''; // All days if empty

    // Modal states
    public $showCreateEditModal = false;
    public $showAttendanceModal = false;

    // Form fields
    public $editingClassId = null;
    public $name = '';
    public $description = '';
    public $trainer_id = '';
    public $day_of_week = 1; // Default Monday
    public $start_time = '08:00';
    public $end_time = '09:00';
    public $capacity = 20;

    // Attendance management state
    public $selectedClass = null;
    public $attendanceDate = '';
    public $attendanceSearch = '';
    public $attendanceRecords = [];

    // Weekday mapper
    public $weekdays = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        0 => 'Domingo'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDay' => ['except' => ''],
    ];

    public function mount()
    {
        $this->attendanceDate = now()->toDateString();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterDay()
    {
        $this->resetPage();
    }

    // Modal triggers
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingClassId = null;
        $this->showCreateEditModal = true;
    }

    public function openEditModal($classId)
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingClassId = $classId;

        $class = GymClass::findOrFail($classId);
        $this->name = $class->name;
        $this->description = $class->description;
        $this->trainer_id = $class->trainer_id;
        $this->day_of_week = $class->day_of_week;
        $this->start_time = substr($class->start_time, 0, 5);
        $this->end_time = substr($class->end_time, 0, 5);
        $this->capacity = $class->capacity;

        $this->showCreateEditModal = true;
    }

    public function saveClass()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trainer_id' => 'required|exists:users,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'required|integer|min:1',
        ]);

        if ($this->editingClassId) {
            $class = GymClass::findOrFail($this->editingClassId);
            $class->update($validated);
            session()->flash('message', 'Clase actualizada exitosamente.');
        } else {
            GymClass::create($validated);
            session()->flash('message', 'Clase creada exitosamente.');
        }

        $this->showCreateEditModal = false;
        $this->resetForm();
    }

    public function deleteClass($classId)
    {
        $class = GymClass::findOrFail($classId);
        $class->delete();
        session()->flash('message', 'Clase eliminada exitosamente.');
    }

    // Attendance Flow
    public function openAttendanceModal($classId)
    {
        $this->selectedClass = GymClass::findOrFail($classId);
        $this->loadAttendance();
        $this->showAttendanceModal = true;
    }

    public function loadAttendance()
    {
        if (!$this->selectedClass) return;

        // Fetch bookings for this class on this date
        $bookings = ClassBooking::where('gym_class_id', $this->selectedClass->id)
            ->where('date', $this->attendanceDate)
            ->with('user')
            ->get();

        // Fetch attendance records for this class on this date
        $records = AttendanceRecord::where('gym_class_id', $this->selectedClass->id)
            ->where('date', $this->attendanceDate)
            ->get()
            ->keyBy('user_id');

        // Compile combined list
        $attendanceList = [];

        // Add booked users
        foreach ($bookings as $booking) {
            $userId = $booking->user_id;
            $attendanceList[$userId] = [
                'user_id' => $userId,
                'name' => $booking->user->name,
                'email' => $booking->user->email,
                'is_booked' => true,
                'status' => $records->has($userId) ? $records[$userId]->status : 'absent'
            ];
        }

        // Add users who checked in but didn't have booking
        foreach ($records as $userId => $record) {
            if (!isset($attendanceList[$userId])) {
                $user = User::find($userId);
                $attendanceList[$userId] = [
                    'user_id' => $userId,
                    'name' => $user ? $user->name : 'Socio Eliminado',
                    'email' => $user ? $user->email : '',
                    'is_booked' => false,
                    'status' => $record->status
                ];
            }
        }

        $this->attendanceRecords = array_values($attendanceList);
    }

    public function toggleAttendance($userId, $status)
    {
        if (!$this->selectedClass) return;

        AttendanceRecord::updateOrCreate(
            [
                'gym_class_id' => $this->selectedClass->id,
                'user_id' => $userId,
                'date' => $this->attendanceDate
            ],
            [
                'status' => $status
            ]
        );

        $this->loadAttendance();
    }

    public function addMemberAttendance($userId)
    {
        if (!$this->selectedClass) return;

        AttendanceRecord::updateOrCreate(
            [
                'gym_class_id' => $this->selectedClass->id,
                'user_id' => $userId,
                'date' => $this->attendanceDate
            ],
            [
                'status' => 'present'
            ]
        );

        $this->attendanceSearch = '';
        $this->loadAttendance();
    }

    public function updatedAttendanceDate()
    {
        $this->loadAttendance();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->trainer_id = '';
        $this->day_of_week = 1;
        $this->start_time = '08:00';
        $this->end_time = '09:00';
        $this->capacity = 20;
        $this->editingClassId = null;
    }

    public function render()
    {
        // Fetch classes query
        $classesQuery = GymClass::with('trainer')
            ->where('name', 'like', '%' . $this->search . '%');

        if ($this->filterDay !== '') {
            $classesQuery->where('day_of_week', $this->filterDay);
        }

        $classes = $classesQuery->orderBy('day_of_week', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        // Fetch trainers for dropdown
        $trainers = User::role(['trainer', 'gym_admin'])->get();

        // Fetch search results for adding custom attendance
        $searchMembers = [];
        if (strlen($this->attendanceSearch) >= 2) {
            $searchMembers = User::role('member')
                ->where(function($q) {
                    $q->where('first_name', 'like', '%' . $this->attendanceSearch . '%')
                      ->orWhere('last_name', 'like', '%' . $this->attendanceSearch . '%')
                      ->orWhere('email', 'like', '%' . $this->attendanceSearch . '%');
                })
                ->limit(5)
                ->get();
        }

        return view('livewire.gym.admin.classes', [
            'classes' => $classes,
            'trainers' => $trainers,
            'searchMembers' => $searchMembers,
        ])->layout('layouts.tenant-app');
    }
}
