<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasSubscription extends Model
{
    protected $fillable = ['tenant_id', 'saas_plan_id', 'start_date', 'end_date', 'status'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function saasPlan()
    {
        return $this->belongsTo(SaasPlan::class);
    }
}
