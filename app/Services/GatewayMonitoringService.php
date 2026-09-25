<?php

namespace App\Services;

use App\Models\Gateway;
use App\Models\ConnectionLog;
use Illuminate\Support\Facades\Process;

class GatewayMonitoringService
{
    /**
     * Memeriksa koneksi gateway.
     */
    public function check(Gateway $gateway): ConnectionLog
    {
        $startTime = microtime(true);

        $result = Process::run([
            'ping',
            '-n',
            '1',
            '-w',
            '2000',
            $gateway->ip_address,
        ]);

        $responseTime = (int) round(
            (microtime(true) - $startTime) * 1000
        );

        $isOnline = $result->successful();

        return ConnectionLog::create([
            'gateway_id' => $gateway->id,
            'status' => $isOnline ? 'online' : 'offline',
            'response_time' => $isOnline ? $responseTime : null,
            'error_message' => $isOnline
                ? null
                : trim($result->errorOutput() ?: 'Gateway tidak dapat dijangkau.'),
            'checked_at' => now(),
        ]);
    }
}