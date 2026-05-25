<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineDay extends Model
{
    protected $fillable = ['routine_id', 'name'];

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function routineExercises()
    {
        return $this->hasMany(RoutineExercise::class)->orderBy('sort_order');
    }
}
