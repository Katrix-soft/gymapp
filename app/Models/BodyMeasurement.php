<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BodyMeasurement extends Model
{
    protected $fillable = ['user_id', 'weight', 'height', 'chest', 'waist', 'fat_percentage', 'logged_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
