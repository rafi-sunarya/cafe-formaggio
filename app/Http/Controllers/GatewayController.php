<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function index()
    {
        $gateways = Gateway::latest()->get();

        return view('targets.index', compact('gateways'));
    }

    public function create()
    {
        return view('targets.create');
    }

    public function edit(Gateway $gateway)
{
    return view('targets.edit', compact('gateway'));
}

public function update(Request $request, Gateway $gateway)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'ip_address' => ['required', 'ip'],
        'description' => ['nullable', 'string'],
    ]);

    $validated['is_active'] = $request->boolean('is_active');

    $gateway->update($validated);

    return redirect()
        ->route('targets.index')
        ->with('success', 'Target berhasil diperbarui.');
}

public function destroy(Gateway $gateway)
{
    $gateway->delete();

    return redirect()
        ->route('targets.index')
        ->with('success', 'Target berhasil dihapus.');
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'ip_address' => ['required', 'ip'],
        'description' => ['nullable', 'string'],
    ]);

    $validated['is_active'] = $request->boolean('is_active');

    Gateway::create($validated);

    return redirect()
        ->route('targets.index')
        ->with('success', 'Target berhasil ditambahkan.');
}
}