<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymClass extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'trainer_id', 'day_of_week', 'start_time', 'end_time', 'capacity'];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function bookings()
    {
        return $this->hasMany(ClassBooking::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function scopeToday($query)
    {
        $dayOfWeek = now()->dayOfWeek;
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function scopeAvailable($query)
    {
        return $query;
    }
}
