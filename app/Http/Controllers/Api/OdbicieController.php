<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RejestracjaOdbiciaService;
use Illuminate\Http\Request;

class OdbicieController extends Controller
{
    public function store(Request $request, RejestracjaOdbiciaService $service)
    {
        $validated = $request->validate([
            'numer_karty' => 'required|string',
            'typ' => 'required|in:WEJSCIE,WYJSCIE',
            'numer_terminala' => 'required|string',
        ]);

        $odbicie = $service->zarejestrujOdbicie($validated['numer_karty'], $validated['typ'], $validated['numer_terminala']);

        return response()->json(['status' => 'OK', 'data' => $odbicie], 201);
    }
}