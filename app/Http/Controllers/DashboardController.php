<?php

namespace App\Http\Controllers;

use App\Models\ConnectionLog;
use App\Models\Gateway;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Target Monitoring
        |--------------------------------------------------------------------------
        */

        // Ambil semua gateway untuk dropdown
        $gateways = Gateway::orderBy('name')->get();

        // Target yang dipilih dari dropdown
        // null = Semua Gateway
        $selectedGatewayId = $request->input('gateway_id');

        /*
        |--------------------------------------------------------------------------
        | Query Monitoring 24 Jam Terakhir
        |--------------------------------------------------------------------------
        */

        $logs24hQuery = ConnectionLog::query()
            ->where('checked_at', '>=', now()->subDay());

        // Filter berdasarkan gateway jika dipilih
        if ($selectedGatewayId) {
            $logs24hQuery->where(
                'gateway_id',
                $selectedGatewayId
            );
        }

        $logs24h = $logs24hQuery
            ->orderBy('checked_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Statistik Monitoring
        |--------------------------------------------------------------------------
        */

        // Total pemeriksaan
        $totalChecks = $logs24h->count();

        // Jumlah pemeriksaan online
        $onlineChecks = $logs24h
            ->where('status', 'online')
            ->count();

        // Uptime
        $uptime = $totalChecks > 0
            ? round(($onlineChecks / $totalChecks) * 100, 1)
            : 0;

        // Rata-rata latency
        $averageLatency = round(
            $logs24h
                ->where('status', 'online')
                ->whereNotNull('response_time')
                ->avg('response_time') ?? 0,
            1
        );

        // Packet loss berdasarkan sampel offline
        $packetLoss = $totalChecks > 0
            ? round(
                (($totalChecks - $onlineChecks) / $totalChecks) * 100,
                1
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Data Terbaru
        |--------------------------------------------------------------------------
        */

        // Pemeriksaan terakhir
        $latestLogQuery = ConnectionLog::with('gateway')
            ->latest('checked_at');

        if ($selectedGatewayId) {
            $latestLogQuery->where(
                'gateway_id',
                $selectedGatewayId
            );
        }

        $latestLog = $latestLogQuery->first();

        // Lima monitoring terbaru
        $latestLogsQuery = ConnectionLog::with('gateway')
            ->latest('checked_at');

        if ($selectedGatewayId) {
            $latestLogsQuery->where(
                'gateway_id',
                $selectedGatewayId
            );
        }

        $latestLogs = $latestLogsQuery
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Total Gangguan
        |--------------------------------------------------------------------------
        */

        $totalIncidents = $logs24h
            ->where('status', 'offline')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Data Grafik
        |--------------------------------------------------------------------------
        */

        // Maksimal 20 data terbaru
        // Data diurutkan dari waktu lama ke baru
        $chartLogs = $logs24h
            ->sortBy('checked_at')
            ->take(-20)
            ->values();

        $chartWidth = 600;
        $chartHeight = 200;

        $chartCount = $chartLogs->count();

        $latencyPoints = [];
        $packetLossPoints = [];

        // Nilai maksimum grafik latency
        $maxLatency = max(
            100,
            (float) ($chartLogs->max('response_time') ?? 100)
        );

        foreach ($chartLogs as $index => $log) {

            // Posisi X
            if ($chartCount > 1) {
                $x = ($index / ($chartCount - 1))
                    * $chartWidth;
            } else {
                $x = $chartWidth / 2;
            }

            /*
            |--------------------------------------------------------------------------
            | Grafik Latency
            |--------------------------------------------------------------------------
            */

            $latency = $log->response_time ?? 0;

            $latencyY = $chartHeight - (
                ($latency / $maxLatency)
                * $chartHeight
            );

            $latencyY = max(
                0,
                min($chartHeight, $latencyY)
            );

            $latencyPoints[] =
                round($x, 2) . ','
                . round($latencyY, 2);

            /*
            |--------------------------------------------------------------------------
            | Grafik Packet Loss
            |--------------------------------------------------------------------------
            */

            // Online = 0%
            // Offline = 100%
            $loss = $log->status === 'online'
                ? 0
                : 100;

            $lossY = $chartHeight - (
                ($loss / 100)
                * $chartHeight
            );

            $packetLossPoints[] =
                round($x, 2) . ','
                . round($lossY, 2);
        }

        // Ubah array menjadi string untuk SVG
        $latencyPoints = implode(
            ' ',
            $latencyPoints
        );

        $packetLossPoints = implode(
            ' ',
            $packetLossPoints
        );

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard.index',
            compact(
                'gateways',
                'selectedGatewayId',
                'totalChecks',
                'onlineChecks',
                'uptime',
                'averageLatency',
                'packetLoss',
                'latestLog',
                'latestLogs',
                'totalIncidents',
                'latencyPoints',
                'packetLossPoints',
                'maxLatency'
            )
        );
    }
}