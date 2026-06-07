<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompetitorController extends Controller
{
    public function index(): View
    {
        $competitors = auth()->user()->competitors()->latest()->get();

        return view('competitors.index', compact('competitors'));
    }

    public function create(): View
    {
        return view('competitors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'building_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'occupancy_rate' => ['nullable', 'numeric', 'between:0,100'],
            'average_rent' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        auth()->user()->competitors()->create($validated);

        return redirect()->route('competitors.index')->with('status', 'Competitor building tracked successfully.');
    }

    public function show(Competitor $competitor): View
    {
        abort_unless(auth()->user()->is($competitor->user), 403);

        return view('competitors.show', compact('competitor'));
    }

    public function edit(Competitor $competitor): View
    {
        abort_unless(auth()->user()->is($competitor->user), 403);

        return view('competitors.edit', compact('competitor'));
    }

    public function update(Request $request, Competitor $competitor): RedirectResponse
    {
        abort_unless(auth()->user()->is($competitor->user), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'building_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'occupancy_rate' => ['nullable', 'numeric', 'between:0,100'],
            'average_rent' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $competitor->update($validated);

        return redirect()->route('competitors.index')->with('status', 'Competitor tracking updated successfully.');
    }

    public function destroy(Competitor $competitor): RedirectResponse
    {
        abort_unless(auth()->user()->is($competitor->user), 403);

        $competitor->delete();

        return redirect()->route('competitors.index')->with('status', 'Competitor removed successfully.');
    }
}
