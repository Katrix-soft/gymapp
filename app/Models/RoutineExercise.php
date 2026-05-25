<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineExercise extends Model
{
    protected $fillable = [
        'routine_day_id', 'exercise_id', 'sets', 'reps', 'weight', 'rest_seconds', 'notes', 'sort_order'
    ];

    public function day()
    {
        return $this->belongsTo(RoutineDay::class, 'routine_day_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
