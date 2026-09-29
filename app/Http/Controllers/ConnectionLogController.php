<?php

namespace App\Http\Controllers;

use App\Models\ConnectionLog;
use App\Models\Gateway;
use App\Services\GatewayMonitoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConnectionLogController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Gateway
        |--------------------------------------------------------------------------
        */

        $gateways = Gateway::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | Gateway yang Dipilih
        |--------------------------------------------------------------------------
        */

        $selectedGatewayId = $request->input('gateway_id');

        /*
        |--------------------------------------------------------------------------
        | Query Riwayat Monitoring
        |--------------------------------------------------------------------------
        */

        $logsQuery = ConnectionLog::with('gateway')
            ->latest('checked_at');

        /*
        |--------------------------------------------------------------------------
        | Filter berdasarkan Gateway
        |--------------------------------------------------------------------------
        */

        if ($selectedGatewayId) {
            $logsQuery->where(
                'gateway_id',
                $selectedGatewayId
            );
        }

        $logs = $logsQuery->get();

        /*
        |--------------------------------------------------------------------------
        | Kirim ke View
        |--------------------------------------------------------------------------
        */

        return view('history.index', compact(
            'logs',
            'gateways',
            'selectedGatewayId'
        ));
    }

    public function check(
        Gateway $gateway,
        GatewayMonitoringService $monitoringService
    ): RedirectResponse {
        $monitoringService->check($gateway);

        return redirect()
            ->route('history.index')
            ->with(
                'success',
                'Monitoring gateway berhasil dijalankan.'
            );
    }
}