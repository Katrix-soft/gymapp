<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasPlan extends Model
{
    protected $fillable = ['name', 'description', 'price', 'limit_members', 'limit_trainers'];
}
