<?php

namespace App\Console\Commands;

use App\Models\Gateway;
use App\Services\GatewayMonitoringService;
use Illuminate\Console\Command;

class MonitorGateways extends Command
{
    /**
     * Nama command.
     */
    protected $signature = 'app:monitor-gateways';

    /**
     * Deskripsi command.
     */
    protected $description = 'Memeriksa koneksi semua gateway yang aktif';

    /**
     * Jalankan monitoring.
     */
    public function handle(GatewayMonitoringService $monitoringService): int
    {
        $gateways = Gateway::where('is_active', true)->get();

        if ($gateways->isEmpty()) {
            $this->warn('Tidak ada gateway aktif untuk dimonitor.');

            return self::SUCCESS;
        }

        foreach ($gateways as $gateway) {
            $log = $monitoringService->check($gateway);

            $this->info(
                "{$gateway->name} ({$gateway->ip_address}) → "
                . strtoupper($log->status)
                . ($log->response_time !== null
                    ? " - {$log->response_time} ms"
                    : '')
            );
        }

        return self::SUCCESS;
    }
}