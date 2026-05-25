<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSetLog extends Model
{
    protected $fillable = ['workout_log_id', 'exercise_id', 'set_number', 'weight', 'reps', 'completed'];

    public function workoutLog()
    {
        return $this->belongsTo(WorkoutLog::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
