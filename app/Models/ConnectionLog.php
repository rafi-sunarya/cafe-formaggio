<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectionLog extends Model
{
    protected $fillable = [
        'gateway_id',
        'status',
        'response_time',
        'error_message',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    /**
     * Relasi ke Gateway.
     */
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }
}