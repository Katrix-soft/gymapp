<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MercadoPagoWebhook extends Model
{
    protected $table = 'mercadopago_webhooks';

    protected $fillable = [
        'webhook_id',
        'topic',
        'resource',
        'payload',
        'status',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
