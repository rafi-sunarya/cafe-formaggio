<?php

namespace App\Http\Controllers;

use App\Models\ConnectionLog;
use App\Models\Gateway;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Periode grafik
        $period = $request->input("period", "24h");

        $periods = [
            "24h" => [
                "label" => "24 Jam",
                "hours" => 24,
            ],
            "7d" => [
                "label" => "7 Hari",
                "hours" => 24 * 7,
            ],
            "30d" => [
                "label" => "30 Hari",
                "hours" => 24 * 30,
            ],
        ];

        // Kalau parameter tidak valid, kembali ke 24 jam
        if (!isset($periods[$period])) {
            $period = "24h";
        }

        $periodHours = $periods[$period]["hours"];

        /*
        |--------------------------------------------------------------------------
        | Target Monitoring
        |--------------------------------------------------------------------------
        */

        // Semua gateway untuk dropdown
        $gateways = Gateway::orderBy("name")->get();

        // Gateway yang dipilih
        // null = Semua Gateway
        $selectedGatewayId = $request->input("gateway_id");
        $period = $request->input("period", "24h");

        $periods = [
            "24h" => now()->subDay(),
            "7d" => now()->subDays(7),
            "30d" => now()->subDays(30),
        ];

        if (!array_key_exists($period, $periods)) {
            $period = "24h";
        }

        $periodStart = $periods[$period];

        $gateways = Gateway::orderBy("name")->get();

        $selectedGateway = $selectedGatewayId
            ? $gateways->firstWhere("id", $selectedGatewayId)
            : null;

        $selectedGateway = $selectedGatewayId
            ? $gateways->firstWhere("id", $selectedGatewayId)
            : null;

        /*
        |--------------------------------------------------------------------------
        | Monitoring 24 Jam Terakhir
        |--------------------------------------------------------------------------
        */

        $logs24hQuery = ConnectionLog::query()->where(
            "checked_at",
            ">=",
            $periodStart
        );

        if ($selectedGatewayId) {
            $logs24hQuery->where("gateway_id", $selectedGatewayId);
        }

        $logs24h = $logs24hQuery->orderBy("checked_at")->get();

        /*
        |--------------------------------------------------------------------------
        | Statistik Monitoring
        |--------------------------------------------------------------------------
        */

        $totalChecks = $logs24h->count();

        $onlineChecks = $logs24h->where("status", "online")->count();

        // Uptime
        $uptime =
            $totalChecks > 0
                ? round(($onlineChecks / $totalChecks) * 100, 1)
                : 0;

        // Rata-rata latency hanya dari pemeriksaan online
        $averageLatency = round(
            $logs24h
                ->where("status", "online")
                ->whereNotNull("response_time")
                ->avg("response_time") ?? 0,
            1
        );

        // Packet loss berdasarkan jumlah sampel offline
        $packetLoss =
            $totalChecks > 0
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

        $latestLogQuery = ConnectionLog::with("gateway")->latest("checked_at");

        if ($selectedGatewayId) {
            $latestLogQuery->where("gateway_id", $selectedGatewayId);
        }

        $latestLog = $latestLogQuery->first();

        $monitoringStatus = $latestLog?->status ?? "offline";

        // Lima monitoring terbaru
        $latestLogsQuery = ConnectionLog::with("gateway")->latest("checked_at");

        if ($selectedGatewayId) {
            $latestLogsQuery->where("gateway_id", $selectedGatewayId);
        }

        $latestLogs = $latestLogsQuery->limit(5)->get();

        /*
        |--------------------------------------------------------------------------
        | Total Gangguan
        |--------------------------------------------------------------------------
        */

        $totalIncidents = $logs24h->where("status", "offline")->count();

        /*
|--------------------------------------------------------------------------
| Data Grafik
|--------------------------------------------------------------------------
*/

        // Ambil maksimal 30 data monitoring terakhir.
        // Dengan interval 30 detik, 30 data = sekitar 15 menit.
        $chartLogs = $logs24h->sortBy("checked_at")->values();

        // Batasi jumlah titik grafik agar tetap ringan.
        $maxChartPoints = 100;

        if ($chartLogs->count() > $maxChartPoints) {
            $step = $chartLogs->count() / $maxChartPoints;

            $sampledLogs = collect();

            for ($i = 0; $i < $maxChartPoints; $i++) {
                $index = (int) floor($i * $step);

                if (isset($chartLogs[$index])) {
                    $sampledLogs->push($chartLogs[$index]);
                }
            }

            $chartLogs = $sampledLogs->values();
        }

        $chartWidth = 600;
        $chartHeight = 180;

        $chartCount = $chartLogs->count();

        $latencyPoints = [];
        $packetLossPoints = [];
        $chartData = [];

        /*
|--------------------------------------------------------------------------
| Skala Latency
|--------------------------------------------------------------------------
*/

        // Cari latency maksimum dari data online.
        $latencyValues = $chartLogs
            ->where("status", "online")
            ->whereNotNull("response_time")
            ->pluck("response_time");

        $maxLatency = max(100, (float) ($latencyValues->max() ?? 100));

        // Beri sedikit ruang di bagian atas grafik.
        $maxLatency = ceil($maxLatency * 1.15);

        /*
|--------------------------------------------------------------------------
| Buat Titik Grafik
|--------------------------------------------------------------------------
*/

        foreach ($chartLogs as $index => $log) {
            /*
    |--------------------------------------------------------------------------
    | Posisi X
    |--------------------------------------------------------------------------
    |
    | Data terakhir dibentangkan dari kiri sampai kanan grafik.
    |
    */

            if ($chartCount > 1) {
                $x = ($index / ($chartCount - 1)) * $chartWidth;
            } else {
                $x = $chartWidth / 2;
            }

            /*
    |--------------------------------------------------------------------------
    | Grafik Latency
    |--------------------------------------------------------------------------
    */

            if ($log->status === "online" && $log->response_time !== null) {
                $latency = (float) $log->response_time;

                // Grafik menggunakan area Y = 20 sampai 200.
                $latencyY = 200 - ($latency / $maxLatency) * 180;

                // Batasi agar tidak keluar dari grafik.
                $latencyY = max(20, min(200, $latencyY));
            } else {
                // Offline tetap berada di bagian bawah grafik.
                $latencyY = 200;
            }

            $latencyPoints[] = round($x, 2) . "," . round($latencyY, 2);

            $chartData[] = [
                "x" => round($x, 2),
                "y" => round($latencyY, 2),
                "latency" => $log->response_time,
                "time" => $log->checked_at->format("H:i:s"),
            ];
            /*
    |--------------------------------------------------------------------------
    | Grafik Packet Loss
    |--------------------------------------------------------------------------
    */

            // Online = 0%
            // Offline = 100%
            $loss = $log->status === "online" ? 0 : 100;

            $lossY = 200 - ($loss / 100) * 180;

            $lossY = max(20, min(200, $lossY));

            $packetLossPoints[] = round($x, 2) . "," . round($lossY, 2);
        }

        /*
|--------------------------------------------------------------------------
| Ubah Array Menjadi String SVG
|--------------------------------------------------------------------------
*/

        $latencyPoints = implode(" ", $latencyPoints);

        $packetLossPoints = implode(" ", $packetLossPoints);

        /*
        |--------------------------------------------------------------------------
        | Return Dashboard
        |--------------------------------------------------------------------------
        */

        return view(
            "dashboard.index",
            compact(
                "gateways",
                "selectedGatewayId",
                "selectedGateway",
                "period",
                "totalChecks",
                "onlineChecks",
                "uptime",
                "averageLatency",
                "packetLoss",
                "latestLog",
                "latestLogs",
                "totalIncidents",
                "latencyPoints",
                "packetLossPoints",
                "maxLatency",
                "monitoringStatus",
                "chartData"
            )
        );
    }
    
public function monitoringUpdates(Request $request)
{
    $periodOptions = [
        '24h' => now()->subDay(),
        '7d' => now()->subDays(7),
        '30d' => now()->subDays(30),
    ];

    $period = $request->query('period', '24h');

    if (!isset($periodOptions[$period])) {
        $period = '24h';
    }

    $periodStart = $periodOptions[$period];
    $gatewayId = $request->query('gateway_id');

    // Status gateway terbaru untuk notifikasi
    $gatewaysQuery = Gateway::query()
        ->where('is_active', true);

    if ($gatewayId) {
        $gatewaysQuery->where('id', $gatewayId);
    }

    $gateways = $gatewaysQuery
        ->with('latestConnectionLog')
        ->get();

    $gatewayUpdates = $gateways->map(function ($gateway) {
        $log = $gateway->latestConnectionLog;

        return [
            'gateway_id' => $gateway->id,
            'gateway_name' => $gateway->name,
            'status' => $log?->status ?? 'unknown',
            'response_time' => $log?->response_time,
            'checked_at' => $log?->checked_at?->toIso8601String(),
        ];
    })->values();

    // Data monitoring sesuai periode dan gateway terpilih
    $logsQuery = ConnectionLog::query()
        ->where('checked_at', '>=', $periodStart);

    if ($gatewayId) {
        $logsQuery->where('gateway_id', $gatewayId);
    }

    $logs = $logsQuery
        ->orderBy('checked_at')
        ->get();

    $totalChecks = $logs->count();

    $onlineChecks = $logs
        ->where('status', 'online')
        ->count();

    $offlineChecks = $totalChecks - $onlineChecks;

    $uptime = $totalChecks > 0
        ? round(($onlineChecks / $totalChecks) * 100, 1)
        : 0;

    $averageLatency = round(
        $logs->where('status', 'online')
            ->whereNotNull('response_time')
            ->avg('response_time') ?? 0,
        1
    );

    $packetLoss = $totalChecks > 0
        ? round(($offlineChecks / $totalChecks) * 100, 1)
        : 0;

    // Data monitoring terbaru
    $latestLogQuery = ConnectionLog::with('gateway')
        ->latest('checked_at');

    if ($gatewayId) {
        $latestLogQuery->where('gateway_id', $gatewayId);
    }

    $latestLog = $latestLogQuery->first();

    $latestLogsQuery = ConnectionLog::with('gateway')
        ->latest('checked_at');

    if ($gatewayId) {
        $latestLogsQuery->where('gateway_id', $gatewayId);
    }

    $latestLogs = $latestLogsQuery
        ->limit(5)
        ->get()
        ->map(function ($log) {
            return [
                'gateway_name' => $log->gateway?->name ?? '-',
                'checked_at' => $log->checked_at
                    ->format('d M Y, H:i:s'),
                'response_time' => $log->response_time,
                'packet_loss' => $log->status === 'online' ? 0 : 100,
                'status' => $log->status,
            ];
        })
        ->values();

    // Siapkan titik grafik
    $chartLogs = $logs->values();

    $maxChartPoints = 100;

    if ($chartLogs->count() > $maxChartPoints) {
        $step = $chartLogs->count() / $maxChartPoints;
        $sampledLogs = collect();

        for ($i = 0; $i < $maxChartPoints; $i++) {
            $index = (int) floor($i * $step);

            if (isset($chartLogs[$index])) {
                $sampledLogs->push($chartLogs[$index]);
            }
        }

        $chartLogs = $sampledLogs->values();
    }

    $chartCount = $chartLogs->count();
    $latencyValues = $chartLogs
        ->where('status', 'online')
        ->whereNotNull('response_time')
        ->pluck('response_time');

    $maxLatency = max(
        100,
        (float) ($latencyValues->max() ?? 100)
    );

    $maxLatency = ceil($maxLatency * 1.15);

    $chartData = $chartLogs->values()->map(
        function ($log, $index) use ($chartCount, $maxLatency) {
            $x = $chartCount > 1
                ? ($index / ($chartCount - 1)) * 600
                : 300;

            $latency = $log->response_time;

            $latencyY = $log->status === 'online'
                && $latency !== null
                ? 200 - ($latency / $maxLatency) * 180
                : 200;

            $latencyY = max(20, min(200, $latencyY));

            $loss = $log->status === 'online' ? 0 : 100;
            $lossY = 200 - ($loss / 100) * 180;
            $lossY = max(20, min(200, $lossY));

            return [
                'x' => round($x, 2),
                'latency_y' => round($latencyY, 2),
                'latency' => $latency,
                'time' => $log->checked_at->format('H:i:s'),
                'loss_y' => round($lossY, 2),
            ];
        }
    );

    return response()->json([
        'success' => true,
        'server_time' => now()->toIso8601String(),
        'period' => $period,
        'gateway_id' => $gatewayId,

        'gateways' => $gatewayUpdates,

        'kpi' => [
            'status' => $latestLog?->status ?? 'offline',
            'average_latency' => $averageLatency,
            'packet_loss' => $packetLoss,
            'uptime' => $uptime,
        ],

        'summary' => [
            'total_checks' => $totalChecks,
            'total_incidents' => $offlineChecks,
        ],

        'latest_log' => [
            'checked_at' => $latestLog?->checked_at
                ?->format('d F Y, H:i:s'),
        ],

        'latest_logs' => $latestLogs,

        'chart' => [
            'max_latency' => $maxLatency,
            'points' => $chartData,
        ],
    ]);
}

}
