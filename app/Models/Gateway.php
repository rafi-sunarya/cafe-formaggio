<?php

namespace App\Models;

use App\Models\ConnectionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gateway extends Model
{
    protected $fillable = [
        'name',
        'ip_address',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke ConnectionLog.
     */
    public function connectionLogs(): HasMany
    {
        return $this->hasMany(ConnectionLog::class);
    }

    /**
     * Mengambil hasil monitoring gateway yang paling terbaru.
     */
    public function latestConnectionLog(): HasOne
    {
        return $this->hasOne(ConnectionLog::class)
            ->latestOfMany('checked_at');
    }
}