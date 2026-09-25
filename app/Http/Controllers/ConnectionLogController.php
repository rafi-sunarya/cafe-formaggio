<?php

namespace App\Http\Controllers;

use App\Models\ConnectionLog;
use App\Models\Gateway;
use App\Services\GatewayMonitoringService;
use Illuminate\Http\RedirectResponse;

class ConnectionLogController extends Controller
{
    public function index()
    {
    $logs = ConnectionLog::with('gateway')
        ->latest('checked_at')
        ->get();

    $gateways = Gateway::orderBy('name')->get();

    return view('history.index', compact('logs', 'gateways'));
    }

    public function check(
        Gateway $gateway,
        GatewayMonitoringService $monitoringService
    ): RedirectResponse {
        $monitoringService->check($gateway);

        return redirect()
            ->route('history.index')
            ->with('success', 'Monitoring gateway berhasil dijalankan.');
    }
}