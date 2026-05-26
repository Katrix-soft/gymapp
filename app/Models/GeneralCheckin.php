<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralCheckin extends Model
{
    protected $table = 'general_checkins';

    protected $fillable = [
        'user_id',
        'checkin_time',
        'checkin_date',
        'method',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
