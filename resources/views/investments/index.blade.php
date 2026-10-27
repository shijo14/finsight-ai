<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">Investment Portfolio</h2>
                <p class="text-xs text-neutral-500 dark:text-slate-400 mt-1">Multi-asset capital allocation, compounding growth & live valuation tracking</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('investments.create') }}" class="inline-flex items-center px-4 py-2 btn-primary rounded-xl shadow-sm text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Investment
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-4 text-emerald-600 dark:text-emerald-400 text-sm font-medium flex items-center justify-between">
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @php
                $totalInvested = $investments->sum('amount_invested');
                $totalCurrent = $investments->sum('current_value');
                $unrealizedGain = $totalCurrent - $totalInvested;
                $roiPct = $totalInvested > 0 ? (($unrealizedGain / $totalInvested) * 100) : 0;
                $assetClasses = $investments->pluck('investment_type')->filter()->unique();
            @endphp

            @if($investments->isNotEmpty())
                {{-- Portfolio KPI Telemetry Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="glass-card p-5 relative overflow-hidden">
                        <div class="text-xs font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Total Invested</div>
                        <div class="mt-2 text-2xl font-bold text-neutral-900 dark:text-white">{{ format_money($totalInvested, 2) }}</div>
                        <div class="mt-1 text-xs text-neutral-400 dark:text-slate-500">Across {{ $investments->count() }} asset holding{{ $investments->count() > 1 ? 's' : '' }}</div>
                    </div>

                    <div class="glass-card p-5 relative overflow-hidden">
                        <div class="text-xs font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Portfolio Valuation</div>
                        <div class="mt-2 text-2xl font-bold text-cyan-600 dark:text-cyan-400">{{ format_money($totalCurrent, 2) }}</div>
                        <div class="mt-1 text-xs text-neutral-400 dark:text-slate-500">Mark-to-market net worth</div>
                    </div>

                    <div class="glass-card p-5 relative overflow-hidden">
                        <div class="text-xs font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Unrealized Delta</div>
                        <div class="mt-2 text-2xl font-bold {{ $unrealizedGain >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                            {{ $unrealizedGain >= 0 ? '+' : '' }}{{ format_money($unrealizedGain, 2) }}
                        </div>
                        <div class="mt-1 text-xs {{ $roiPct >= 0 ? 'text-emerald-500/80' : 'text-rose-400' }}">
                            {{ $roiPct >= 0 ? '+' : '' }}{{ number_format($roiPct, 2) }}% all-time growth
                        </div>
                    </div>

                    <div class="glass-card p-5 relative overflow-hidden">
                        <div class="text-xs font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Asset Classes</div>
                        <div class="mt-2 text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $assetClasses->count() }} Diversified</div>
                        <div class="mt-1 text-xs text-neutral-400 dark:text-slate-500">{{ $assetClasses->take(3)->implode(', ') }}</div>
                    </div>
                </div>
            @endif

            <div class="glass-card overflow-hidden">
                <div class="p-6">
                    @if($investments->isEmpty())
                        <div class="rounded-xl border border-dashed border-neutral-300 dark:border-white/20 p-12 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-cyan-500/10 text-cyan-500 flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-slate-100">No investments recorded yet</h3>
                            <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400 max-w-md mx-auto">Start building long-term wealth by tracking your stocks, mutual funds, gold, fixed deposits, or tax-saving ELSS investments.</p>
                            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ route('investments.create') }}" class="inline-flex items-center px-4 py-2 btn-primary rounded-xl text-sm font-medium">Add First Investment</a>
                                <a href="{{ route('chat.index') }}" class="inline-flex items-center px-4 py-2 bg-neutral-200 dark:bg-white/10 hover:bg-neutral-300 dark:hover:bg-white/20 text-neutral-800 dark:text-white rounded-xl text-sm font-medium transition">
                                    Ask AI to Log Investment
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-neutral-200 dark:divide-white/10">
                                <thead class="bg-neutral-100 dark:bg-white/5">
                                    <tr>
                                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Asset Class</th>
                                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Holding Name</th>
                                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Invested Capital</th>
                                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Current Value</th>
                                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Unrealized Gain</th>
                                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-neutral-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-200 dark:divide-white/10">
                                    @foreach($investments as $investment)
                                        @php
                                            $itemGain = $investment->current_value - $investment->amount_invested;
                                            $itemRoi = $investment->amount_invested > 0 ? (($itemGain / $investment->amount_invested) * 100) : 0;
                                        @endphp
                                        <tr class="hover:bg-neutral-50 dark:hover:bg-white/[0.02] transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-neutral-200 dark:bg-white/10 text-neutral-800 dark:text-slate-200">
                                                    {{ $investment->investment_type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-slate-100">
                                                {{ $investment->name }}
                                                @if($investment->investment_date)
                                                    <div class="text-xs text-neutral-400 dark:text-slate-500 font-normal mt-0.5">
                                                        {{ $investment->investment_date->format('d M Y') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-700 dark:text-slate-300">{{ format_money($investment->amount_invested, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-neutral-900 dark:text-white">{{ format_money($investment->current_value, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="font-medium {{ $itemGain >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                                    {{ $itemGain >= 0 ? '+' : '' }}{{ format_money($itemGain, 2) }}
                                                </div>
                                                <div class="text-xs {{ $itemRoi >= 0 ? 'text-emerald-500/80' : 'text-rose-400' }}">
                                                    {{ $itemRoi >= 0 ? '+' : '' }}{{ number_format($itemRoi, 2) }}%
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                                <a href="{{ route('investments.edit', $investment) }}" class="text-cyan-600 dark:text-cyan-400 hover:text-cyan-700 dark:hover:text-cyan-300">Edit</a>
                                                <form action="{{ route('investments.destroy', $investment) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 transition" onclick="return confirm('Delete this investment?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
