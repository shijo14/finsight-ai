<?php

namespace App\Http\Controllers;

use App\Models\FinancialTimeline;
use App\Services\MultiverseEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MultiverseController extends Controller
{
    /**
     * Display the Multiverse Studio dashboard.
     */
    public function index(Request $request, MultiverseEngineService $engine): View
    {
        $user = auth()->user();
        $baseline = $engine->getUserFinancialBaseline($user->id);
        $timelines = $engine->ensureDefaultPresets($user);

        // Determine currently active/selected branch
        $requestedSlug = $request->query('branch');
        if ($requestedSlug) {
            $selected = $timelines->firstWhere('slug', $requestedSlug);
        } else {
            $selected = $timelines->firstWhere('is_active_preview', true) ?? $timelines->first();
        }

        if (!$selected) {
            $selected = $timelines->first();
        }

        $horizon = $selected ? $selected->target_horizon_years : 15;

        // Construct virtual baseline timeline for "main"
        $mainTimeline = new FinancialTimeline([
            'name'                    => 'Main Reality (Status Quo)',
            'slug'                    => 'main',
            'income_multiplier'       => 1.00,
            'expense_multiplier'      => 1.00,
            'lump_sum_inflow'         => 0.00,
            'lump_sum_outflow'        => 0.00,
            'monthly_investment_rate' => 20.00,
            'market_cagr'             => 10.00,
            'target_horizon_years'    => $horizon,
        ]);

        $mainProj = $mainTimeline->projectTrajectory(
            $baseline['net_worth'],
            $baseline['monthly_income'],
            $baseline['monthly_expense']
        );

        $branchProj = $selected->projectTrajectory(
            $baseline['net_worth'],
            $baseline['monthly_income'],
            $baseline['monthly_expense']
        );
        $branchProj['timeline_model'] = $selected;

        $diff = $engine->computeDiff($mainProj, $branchProj, $baseline);

        return view('multiverse.index', [
            'baseline'   => $baseline,
            'timelines'  => $timelines,
            'selected'   => $selected,
            'mainProj'   => $mainProj,
            'branchProj' => $branchProj,
            'diff'       => $diff,
        ]);
    }

    /**
     * Fork a new timeline branch.
     */
    public function fork(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:100',
            'income_multiplier'       => 'required|numeric|min:0|max:5',
            'expense_multiplier'      => 'required|numeric|min:0.1|max:5',
            'lump_sum_inflow'         => 'nullable|numeric|min:0',
            'lump_sum_outflow'        => 'nullable|numeric|min:0',
            'monthly_investment_rate' => 'required|numeric|min:0|max:100',
            'market_cagr'             => 'required|numeric|min:0|max:40',
            'target_horizon_years'    => 'required|integer|min:5|max:30',
            'description'             => 'nullable|string|max:300',
        ]);

        $user = auth()->user();
        $slug = Str::slug($validated['name']);

        // Check if slug exists
        if ($user->timelines()->where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        // Unset previous active preview
        $user->timelines()->update(['is_active_preview' => false]);

        $timeline = $user->timelines()->create([
            'name'                    => $validated['name'],
            'slug'                    => $slug,
            'description'             => $validated['description'] ?? "Forked timeline with custom economic parameters.",
            'income_multiplier'       => $validated['income_multiplier'],
            'expense_multiplier'      => $validated['expense_multiplier'],
            'lump_sum_inflow'         => $validated['lump_sum_inflow'] ?? 0,
            'lump_sum_outflow'        => $validated['lump_sum_outflow'] ?? 0,
            'monthly_investment_rate' => $validated['monthly_investment_rate'],
            'market_cagr'             => $validated['market_cagr'],
            'target_horizon_years'    => $validated['target_horizon_years'],
            'is_active_preview'       => true,
            'status'                  => 'simulated',
        ]);

        return redirect()->route('multiverse.index', ['branch' => $timeline->slug])
            ->with('status', "🌿 Forked new timeline branch '{$timeline->name}'! Parallel trajectory generated.");
    }

    /**
     * 1-Click "Merge Timeline into Reality" (git merge).
     */
    public function merge(int $id, MultiverseEngineService $engine): RedirectResponse
    {
        $user = auth()->user();
        $timeline = $user->timelines()->findOrFail($id);

        $baseline = $engine->getUserFinancialBaseline($user->id);
        $branchProj = $timeline->projectTrajectory(
            $baseline['net_worth'],
            $baseline['monthly_income'],
            $baseline['monthly_expense']
        );

        $mergeResult = $engine->mergeTimelineToReality($user, $timeline, $baseline, $branchProj);

        return redirect()->route('multiverse.index', ['branch' => $timeline->slug])
            ->with('status', "🚀 {$mergeResult['message']}");
    }

    /**
     * Future-Self AI conversational terminal endpoint.
     */
    public function futureSelfChat(Request $request, int $id, MultiverseEngineService $engine): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $timeline = $user->timelines()->findOrFail($id);

        $baseline = $engine->getUserFinancialBaseline($user->id);
        $mainTimeline = new FinancialTimeline([
            'target_horizon_years' => $timeline->target_horizon_years,
        ]);
        $mainProj = $mainTimeline->projectTrajectory(
            $baseline['net_worth'],
            $baseline['monthly_income'],
            $baseline['monthly_expense']
        );
        $branchProj = $timeline->projectTrajectory(
            $baseline['net_worth'],
            $baseline['monthly_income'],
            $baseline['monthly_expense']
        );
        $branchProj['timeline_model'] = $timeline;

        $diff = $engine->computeDiff($mainProj, $branchProj, $baseline);

        $reply = $engine->generateFutureSelfDialogue(
            $user,
            $timeline,
            $request->input('message'),
            $diff
        );

        return response()->json([
            'success'     => true,
            'reply'       => $reply,
            'timeline'    => $timeline->name,
            'freedom_age' => $diff['freedom_age_branch'] ?? 44,
            'delta_10y'   => format_money($diff['delta_10y'] ?? 0),
        ]);
    }

    /**
     * Delete an experimental branch.
     */
    public function destroy(int $id): RedirectResponse
    {
        $timeline = auth()->user()->timelines()->findOrFail($id);
        $name = $timeline->name;
        $timeline->delete();

        return redirect()->route('multiverse.index')
            ->with('status', "🗑️ Timeline '{$name}' deleted.");
    }
}
