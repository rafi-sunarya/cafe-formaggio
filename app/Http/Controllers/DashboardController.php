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
}
