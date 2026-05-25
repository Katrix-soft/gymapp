<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    protected $fillable = ['name', 'description', 'trainer_id', 'member_id'];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function days()
    {
        return $this->hasMany(RoutineDay::class)->orderBy('id');
    }
}
