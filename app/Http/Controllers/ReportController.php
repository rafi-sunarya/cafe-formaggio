<?php

namespace App\Http\Controllers;

use App\Models\ConnectionLog;
use App\Models\Gateway;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $gateways = Gateway::orderBy('name')->get();

        $selectedGatewayId = $request->input('gateway_id');

        $period = $request->input('period', '24h');

        /*
        |--------------------------------------------------------------------------
        | Tentukan Periode
        |--------------------------------------------------------------------------
        */

        switch ($period) {
            case '7d':
                $startDate = now()->subDays(7);
                $periodLabel = '7 Hari Terakhir';
                break;

            case '30d':
                $startDate = now()->subDays(30);
                $periodLabel = '30 Hari Terakhir';
                break;

            default:
                $period = '24h';
                $startDate = now()->subDay();
                $periodLabel = '24 Jam Terakhir';
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Query Monitoring
        |--------------------------------------------------------------------------
        */

        $logsQuery = ConnectionLog::with('gateway')
            ->where('checked_at', '>=', $startDate)
            ->latest('checked_at');

        if ($selectedGatewayId) {
            $logsQuery->where(
                'gateway_id',
                $selectedGatewayId
            );
        }

        $logs = $logsQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $totalChecks = $logs->count();

        $onlineChecks = $logs
            ->where('status', 'online')
            ->count();

        $offlineChecks = $logs
            ->where('status', 'offline')
            ->count();

        $uptime = $totalChecks > 0
            ? round(
                ($onlineChecks / $totalChecks) * 100,
                1
            )
            : 0;

        $packetLoss = $totalChecks > 0
            ? round(
                ($offlineChecks / $totalChecks) * 100,
                1
            )
            : 0;

        $averageLatency = round(
            $logs
                ->where('status', 'online')
                ->whereNotNull('response_time')
                ->avg('response_time') ?? 0,
            1
        );

        /*
        |--------------------------------------------------------------------------
        | Gateway yang Dipilih
        |--------------------------------------------------------------------------
        */

        $selectedGateway = $selectedGatewayId
            ? Gateway::find($selectedGatewayId)
            : null;

        $latestLog = $selectedGatewayId
    ? ConnectionLog::where('gateway_id', $selectedGatewayId)
        ->latest('checked_at')
        ->first()
    : ConnectionLog::latest('checked_at')->first();

        /*
        |--------------------------------------------------------------------------
        | Data Grafik
        |--------------------------------------------------------------------------
        */

        $chartLogs = $logs
            ->sortBy('checked_at')
            ->take(-30)
            ->values();

        $chartData = $chartLogs->map(function ($log) {
            return [
                'time' => $log->checked_at->format('H:i'),
                'latency' => $log->response_time,
                'status' => $log->status,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('reports.index', compact(
            'gateways',
            'selectedGatewayId',
            'selectedGateway',
            'latestLog',
            'period',
            'periodLabel',
            'logs',
            'totalChecks',
            'onlineChecks',
            'offlineChecks',
            'uptime',
            'packetLoss',
            'averageLatency',
            'chartData'
        ));
    }
}