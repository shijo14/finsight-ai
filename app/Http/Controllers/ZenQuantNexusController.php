<?php

namespace App\Http\Controllers;

use App\Services\ZenQuantEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZenQuantNexusController extends Controller
{
    /**
     * Render the Zen & Quant Nexus Dashboard.
     */
    public function index(Request $request, ZenQuantEngineService $engine): View
    {
        $user = auth()->user();

        $velocity = $engine->calculateEscapeVelocity($user);
        $kakeibo  = $engine->analyzeKakeiboRadar($user);
        $yuebao   = $engine->calculateYueBaoArbitrage($user);
        $recentDeliberations = $user->warRoomDeliberations()->orderByDesc('created_at')->limit(5)->get();

        return view('nexus.index', [
            'velocity'            => $velocity,
            'kakeibo'             => $kakeibo,
            'yuebao'              => $yuebao,
            'recentDeliberations' => $recentDeliberations,
        ]);
    }

    /**
     * API Endpoint to summon the Tri-Agent Asian War Room.
     */
    public function summonCouncil(Request $request, ZenQuantEngineService $engine): JsonResponse
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:150',
            'amount'    => 'required|numeric|min:1',
            'category'  => 'nullable|string|max:100',
            'reason'    => 'nullable|string|max:400',
        ]);

        $user = auth()->user();
        $result = $engine->deliberateWarRoom(
            $user,
            $validated['item_name'],
            (float) $validated['amount'],
            $validated['category'] ?? 'Shopping',
            $validated['reason'] ?? null
        );

        return response()->json($result);
    }

    /**
     * API Endpoint for live millisecond escape velocity telemetry.
     */
    public function getLiveTelemetry(Request $request, ZenQuantEngineService $engine): JsonResponse
    {
        $user = auth()->user();
        $burn = $request->query('burn') !== null ? (float) $request->query('burn') : null;
        $assets = $request->query('assets') !== null ? (float) $request->query('assets') : null;
        $cagr = $request->query('cagr') !== null ? (float) $request->query('cagr') : null;

        $telemetry = $engine->calculateEscapeVelocity($user, $burn, $assets, $cagr);

        return response()->json([
            'success'   => true,
            'telemetry' => $telemetry,
        ]);
    }
}
