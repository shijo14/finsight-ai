<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GoalController extends Controller
{
    public function index(): View
    {
        $goals = auth()->user()->goals()->latest('created_at')->get();

        return view('goals.index', compact('goals'));
    }

    public function create(): View
    {
        return view('goals.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $validated['current_amount'] = $validated['current_amount'] ?? 0;

        auth()->user()->goals()->create($validated);

        return redirect()->route('goals.index')->with('status', 'Goal created successfully.');
    }

    public function show(Goal $goal): View
    {
        abort_unless(auth()->user()->is($goal->user), 403);

        return view('goals.show', compact('goal'));
    }

    public function edit(Goal $goal): View
    {
        abort_unless(auth()->user()->is($goal->user), 403);

        return view('goals.edit', compact('goal'));
    }

    public function update(Request $request, Goal $goal): RedirectResponse
    {
        abort_unless(auth()->user()->is($goal->user), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $validated['current_amount'] = $validated['current_amount'] ?? 0;

        $goal->update($validated);

        return redirect()->route('goals.index')->with('status', 'Goal updated successfully.');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        abort_unless(auth()->user()->is($goal->user), 403);

        $goal->delete();

        return redirect()->route('goals.index')->with('status', 'Goal removed successfully.');
    }
}
