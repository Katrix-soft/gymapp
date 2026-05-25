<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public $incrementing = false;
    protected $keyType = 'string';

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'owner_email',
        ];
    }

    public function saasSubscriptions()
    {
        return $this->hasMany(SaasSubscription::class);
    }

    public function activeSaasSubscription()
    {
        return $this->hasOne(SaasSubscription::class)->where('status', 'active')->latestOfMany();
    }
}
