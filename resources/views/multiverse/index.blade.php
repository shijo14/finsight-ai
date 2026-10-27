<x-app-layout>
    <div x-data="multiverseApp()" @open-fork-modal.window="showForkModal = true" class="py-6">
        <x-slot name="header">
            <div x-data class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🌌</span>
                        <h2 class="font-bold text-xl text-neutral-900 dark:text-white leading-tight">
                            Financial Multiverse: Git for Your Money & AI Digital Twin
                        </h2>
                    </div>
                    <p class="text-xs text-neutral-500 dark:text-slate-400 mt-1">
                        Fork parallel economic realities, inspect financial diffs, chat with your future self, and merge winning futures into reality.
                    </p>
                </div>

                {{-- Status Pills --}}
                <div class="flex items-center gap-3">
                    <div class="hidden sm:block text-right bg-purple-500/10 border border-purple-500/20 px-3.5 py-1.5 rounded-xl">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-purple-600 dark:text-purple-400 block">Baseline Net Worth</span>
                        <span class="text-sm font-extrabold text-neutral-900 dark:text-white">Rs {{ number_format($baseline['net_worth']) }}</span>
                    </div>

                    <button @click="$dispatch('open-fork-modal')" 
                            onclick="window.dispatchEvent(new CustomEvent('open-fork-modal'))" 
                            type="button"
                            class="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 shadow-purple-500/20 bg-purple-600 hover:bg-purple-500 cursor-pointer">
                        <span>🌿</span>
                        <span>Fork New Branch</span>
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash status notification --}}
            @if(session('status'))
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-2xl flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('status') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white text-xs">&times;</button>
            </div>
            @endif

            {{-- Branch Selector Bar (git checkout) --}}
            <div class="glass-card p-3 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-mono font-bold text-neutral-400 flex items-center gap-1 pl-2">
                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <span>branches:</span>
                    </span>

                    {{-- Main Baseline Branch Tag --}}
                    <div class="px-3 py-1.5 rounded-lg text-xs font-mono font-medium border border-neutral-300/40 dark:border-white/10 text-neutral-600 dark:text-neutral-400 bg-neutral-100/50 dark:bg-white/5 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        <span>main (reality)</span>
                    </div>

                    {{-- User Timeline Branches --}}
                    @foreach($timelines as $t)
                    <a href="{{ route('multiverse.index', ['branch' => $t->slug]) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-mono font-medium transition-all flex items-center gap-1.5 {{ $selected->id === $t->id ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : 'border border-neutral-200 dark:border-white/10 text-neutral-700 dark:text-slate-300 hover:bg-neutral-100 dark:hover:bg-white/5' }}">
                        <span>🌿</span>
                        <span>{{ $t->slug }}</span>
                        @if($t->status === 'merged')
                            <span class="px-1.5 py-0.2 rounded text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">merged</span>
                        @endif
                    </a>
                    @endforeach
                </div>

                {{-- Active Branch Delete button if custom --}}
                @if($selected && !in_array($selected->slug, ['fire-at-42', 'startup-leap', 'mega-real-estate']))
                <form action="{{ route('multiverse.destroy', $selected->id) }}" method="POST" onsubmit="return confirm('Delete this timeline branch?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-400 px-2 py-1">Delete Branch</button>
                </form>
                @endif
            </div>

            {{-- Main Studio Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Left 7 Columns: Divergence Chart + Terminal Diff --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Interactive Divergence Chart --}}
                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="section-label">Trajectory Divergence</span>
                                    <span class="text-xs text-purple-400 font-mono font-bold">{{ $selected->name }}</span>
                                </div>
                                <h3 class="text-base font-bold text-neutral-900 dark:text-white mt-1">
                                    Net Worth Projection vs Main Reality
                                </h3>
                            </div>

                            <div class="flex items-center gap-4 text-xs font-mono">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3 h-1 bg-cyan-400 rounded-full"></span>
                                    <span class="text-neutral-500 dark:text-slate-400">main</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3 h-1 bg-purple-500 rounded-full"></span>
                                    <span class="text-purple-400 font-bold">{{ $selected->slug }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3 h-0.5 border-t border-dashed border-amber-400"></span>
                                    <span class="text-amber-400">FI/RE Target</span>
                                </div>
                            </div>
                        </div>

                        <div class="relative h-72 w-full">
                            <canvas id="multiverseChart"></canvas>
                        </div>

                        <div class="mt-4 pt-3 border-t border-neutral-100 dark:border-white/5 flex items-center justify-between text-xs text-neutral-500 dark:text-slate-400">
                            <span>Horizon: {{ $selected->target_horizon_years }} Years</span>
                            <span>Assumed Equity CAGR: {{ $selected->market_cagr }}%</span>
                            <span>Monthly Investment: {{ $selected->monthly_investment_rate }}% of surplus</span>
                        </div>
                    </div>

                    {{-- Terminal-Style Financial Diff (git diff) --}}
                    <div class="rounded-2xl overflow-hidden border border-slate-800 bg-[#0d1117] shadow-2xl">
                        {{-- Terminal Window Header --}}
                        <div class="px-4 py-2.5 bg-[#161b22] border-b border-slate-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500/80 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80 inline-block"></span>
                                <span class="text-xs font-mono text-slate-400 ml-2 font-medium">git diff reality/main..reality/{{ $selected->slug }}</span>
                            </div>
                            <span class="text-[10px] font-mono text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded border border-purple-500/20">
                                {{ $diff['freedom_delta'] > 0 ? '+' . $diff['freedom_delta'] . ' yrs freedom' : 'timeline active' }}
                            </span>
                        </div>

                        {{-- Terminal Code Area --}}
                        <div class="p-4 font-mono text-xs leading-relaxed overflow-x-auto text-slate-300 space-y-1">
                            <div class="text-slate-500">diff --git a/reality/main b/reality/{{ $selected->slug }}</div>
                            <div class="text-slate-500">--- a/reality/main</div>
                            <div class="text-slate-500">+++ b/reality/{{ $selected->slug }}</div>
                            <div class="text-cyan-400">@@ -1,5 +1,5 @@ Financial Trajectory Delta</div>
                            
                            <div class="{{ $diff['delta_5y'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $diff['delta_5y'] >= 0 ? '+' : '-' }} Net Worth at 5 Years: {{ format_money(abs($diff['delta_5y'])) }} {{ $diff['delta_5y'] >= 0 ? '[SURPLUS]' : '[DEFICIT]' }}
                            </div>
                            <div class="{{ $diff['delta_10y'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $diff['delta_10y'] >= 0 ? '+' : '-' }} Net Worth at 10 Years: {{ format_money(abs($diff['delta_10y'])) }} (Projected Total: {{ format_money($diff['branch_10y_total']) }})
                            </div>
                            <div class="{{ $diff['freedom_delta'] >= 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                                {{ $diff['freedom_delta'] >= 0 ? '+' : '-' }} Financial Freedom: {{ $diff['freedom_age_branch'] ? 'Age ' . $diff['freedom_age_branch'] : 'Age 65+' }} ({{ abs($diff['freedom_delta']) }} years {{ $diff['freedom_delta'] >= 0 ? 'earlier' : 'delayed' }})
                            </div>
                            <div class="{{ $diff['monthly_burn_delta'] <= 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                                {{ $diff['monthly_burn_delta'] <= 0 ? '+' : '-' }} Monthly Burn: {{ $diff['monthly_burn_delta'] <= 0 ? '-' : '+' }}{{ format_money(abs($diff['monthly_burn_delta'])) }}/month discipline shift
                            </div>
                            <div class="text-purple-400">
                                + Equity Compounding: {{ $selected->market_cagr }}% CAGR across {{ $selected->monthly_investment_rate }}% monthly allocation
                            </div>
                        </div>

                        {{-- Metric Tiles Footer --}}
                        <div class="grid grid-cols-3 divide-x divide-slate-800 border-t border-slate-800 bg-[#0d1117] p-3 text-center">
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">10-Year Delta</span>
                                <span class="text-sm font-bold {{ $diff['delta_10y'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $diff['delta_10y'] >= 0 ? '+' : '' }}{{ format_money($diff['delta_10y']) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Freedom Age</span>
                                <span class="text-sm font-bold text-cyan-400">
                                    {{ $diff['freedom_age_branch'] ? 'Age ' . $diff['freedom_age_branch'] : 'Age 60+' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Monthly Shift</span>
                                <span class="text-sm font-bold {{ $diff['monthly_burn_delta'] <= 0 ? 'text-emerald-400' : 'text-amber-400' }}">
                                    {{ $diff['monthly_burn_delta'] <= 0 ? '-' : '+' }}{{ format_money(abs($diff['monthly_burn_delta'])) }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right 5 Columns: Future-Self AI Hologram + Merge Action --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Future-Self AI Dialogue Terminal --}}
                    <div class="glass-card p-6 border-purple-500/30 flex flex-col justify-between h-[420px] bg-gradient-to-b from-purple-950/20 via-slate-900/40 to-slate-950/30">
                        {{-- Avatar Header --}}
                        <div class="flex items-center justify-between pb-3 border-b border-neutral-100 dark:border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-cyan-500 flex items-center justify-center text-lg shadow-lg shadow-purple-500/20">
                                    🧠
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h4 class="text-sm font-bold text-neutral-900 dark:text-white">Future Self (Digital Twin)</h4>
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                    </div>
                                    <p class="text-[11px] text-purple-400 font-mono">
                                        Living in {{ $selected->name }} (Age {{ $diff['freedom_age_branch'] ?? 44 }})
                                    </p>
                                </div>
                            </div>

                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 bg-white/5 px-2 py-0.5 rounded-full border border-white/10">
                                Year 2038
                            </span>
                        </div>

                        {{-- Dialogue Scroll Area --}}
                        <div class="flex-1 overflow-y-auto py-3 space-y-3 pr-1 text-xs" id="chatStream">
                            {{-- Initial Future Self Greeting --}}
                            <div class="flex items-start gap-2.5">
                                <div class="w-6 h-6 rounded-lg bg-purple-600/30 text-purple-300 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">
                                    YOU
                                </div>
                                <div class="bg-purple-900/30 border border-purple-500/20 p-3 rounded-2xl rounded-tl-sm text-slate-200 leading-relaxed max-w-[85%]">
                                    "I am living the consequences of <strong>{{ $selected->name }}</strong> right now.
                                    @if($diff['delta_10y'] >= 0)
                                    Because you committed to this timeline back in 2026, we unlocked financial freedom at age {{ $diff['freedom_age_branch'] ?? 42 }}. The early sacrifices were hard, but waking up debt-free with compounding investments changed our entire life."
                                    @else
                                    This path was risky. We took on heavy capital outlays and our cash reserves dipped, requiring extra discipline. Ask me how we navigated it."
                                    @endif
                                </div>
                            </div>

                            {{-- Render Dynamic Messages --}}
                            <template x-for="(msg, i) in chatMessages" :key="i">
                                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex items-start gap-2.5'">
                                    <template x-if="msg.role === 'bot'">
                                        <div class="w-6 h-6 rounded-lg bg-purple-600/30 text-purple-300 flex items-center justify-center flex-shrink-0 text-[10px] font-bold">YOU</div>
                                    </template>
                                    <div :class="msg.role === 'user' ? 'bg-cyan-600 text-white p-2.5 rounded-2xl rounded-tr-sm max-w-[85%]' : 'bg-purple-900/30 border border-purple-500/20 p-3 rounded-2xl rounded-tl-sm text-slate-200 leading-relaxed max-w-[85%]'" x-text="msg.text"></div>
                                </div>
                            </template>

                            <template x-if="isThinking">
                                <div class="flex items-center gap-2 text-purple-400 text-xs italic pl-8">
                                    <span class="animate-pulse">Future Self transmitting telemetry...</span>
                                </div>
                            </template>
                        </div>

                        {{-- Quick Prompt Chips --}}
                        <div class="py-1 flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                            <button @click="askFutureSelf('What was the hardest sacrifice you made?')" type="button" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-white/5 hover:bg-purple-500/20 border border-white/10 text-[10px] text-slate-300 transition-all">
                                💭 What was hardest?
                            </button>
                            <button @click="askFutureSelf('Did our index fund investments actually compound?')" type="button" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-white/5 hover:bg-purple-500/20 border border-white/10 text-[10px] text-slate-300 transition-all">
                                📈 Did it compound?
                            </button>
                            <button @click="askFutureSelf('Should I merge this timeline right now?')" type="button" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-white/5 hover:bg-purple-500/20 border border-white/10 text-[10px] text-slate-300 transition-all">
                                🚀 Should I merge?
                            </button>
                        </div>

                        {{-- Chat Input Bar --}}
                        <form @submit.prevent="submitChat()" class="mt-2 flex items-center gap-2">
                            <input type="text" x-model="userInput" placeholder="Ask your Future Self in Year 2038..." class="glass-input text-xs py-2 px-3 flex-1" :disabled="isThinking" />
                            <button type="submit" class="btn-primary text-xs py-2 px-3.5 bg-purple-600 hover:bg-purple-500" :disabled="isThinking || !userInput.trim()">
                                <span>Send</span>
                            </button>
                        </form>
                    </div>

                    {{-- 1-Click "Merge Timeline into Reality" Action Card --}}
                    <div class="glass-card p-6 border-emerald-500/30 relative overflow-hidden bg-gradient-to-br from-emerald-950/20 via-slate-900/40 to-slate-950/20">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">🚀</span>
                                <span class="section-label text-emerald-500 dark:text-emerald-400">Execution Protocol</span>
                            </div>
                            @if($selected->status === 'merged')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    Active Reality Merged
                                </span>
                            @endif
                        </div>

                        <h3 class="text-base font-bold text-neutral-900 dark:text-white">
                            Merge '{{ $selected->name }}' into Reality (git merge)
                        </h3>
                        <p class="text-xs text-neutral-500 dark:text-slate-400 mt-1 leading-relaxed">
                            Turn this future into your real life. Merging will dynamically adjust your active monthly budgets ({{ $selected->expense_multiplier }}x multiplier) and anchor a 5-year strategic Goal in your command center.
                        </p>

                        <div class="mt-4 pt-4 border-t border-neutral-100 dark:border-white/5 flex items-center justify-between">
                            <div class="text-xs text-neutral-400">
                                <span>Target: </span>
                                <strong class="text-emerald-400">{{ format_money($diff['branch_10y_total']) }}</strong>
                            </div>

                            <form action="{{ route('multiverse.merge', $selected->id) }}" method="POST" onsubmit="return confirm('Merge this timeline into your active budget and goals?');">
                                @csrf
                                <button type="submit" class="btn-primary text-xs py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 shadow-emerald-500/20 flex items-center gap-1.5 font-bold">
                                    <span>🚀 Merge to Reality</span>
                                    <span>&rarr;</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- Fork Timeline Modal (git branch) --}}
        <div x-show="showForkModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" @click.self="showForkModal = false">
            <div class="glass-card max-w-lg w-full p-6 border-purple-500/30 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-neutral-200 dark:border-white/10">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🌿</span>
                        <h3 class="font-bold text-base text-neutral-900 dark:text-white">Fork New Financial Reality</h3>
                    </div>
                    <button type="button" @click="showForkModal = false" class="text-neutral-400 hover:text-white text-lg">&times;</button>
                </div>

                <form action="{{ route('multiverse.fork') }}" method="POST" class="mt-4 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Timeline Branch Name</label>
                        <input type="text" name="name" placeholder="e.g. Frugal Nomad at 38" required class="glass-input w-full text-xs" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Income Multiplier (1.0 = current)</label>
                            <input type="number" step="0.05" min="0" max="5" name="income_multiplier" value="1.20" required class="glass-input w-full text-xs" />
                            <span class="text-[10px] text-neutral-400">1.20 = +20% income</span>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Expense Multiplier (1.0 = current)</label>
                            <input type="number" step="0.05" min="0.1" max="5" name="expense_multiplier" value="0.80" required class="glass-input w-full text-xs" />
                            <span class="text-[10px] text-neutral-400">0.80 = -20% burn</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Lump Sum Inflow (Rs)</label>
                            <input type="number" step="1000" min="0" name="lump_sum_inflow" value="0" class="glass-input w-full text-xs" />
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Lump Sum Outflow (Rs)</label>
                            <input type="number" step="1000" min="0" name="lump_sum_outflow" value="0" class="glass-input w-full text-xs" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Monthly Investment Rate (%)</label>
                            <input type="number" step="1" min="0" max="100" name="monthly_investment_rate" value="35" required class="glass-input w-full text-xs" />
                            <span class="text-[10px] text-neutral-400">% of surplus invested</span>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Expected Equity CAGR (%)</label>
                            <input type="number" step="0.5" min="1" max="40" name="market_cagr" value="12" required class="glass-input w-full text-xs" />
                            <span class="text-[10px] text-neutral-400">Nifty 50 historical ~12%</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Simulation Horizon (Years)</label>
                        <select name="target_horizon_years" class="glass-input w-full text-xs">
                            <option value="5">5 Years</option>
                            <option value="10">10 Years</option>
                            <option value="15" selected>15 Years</option>
                            <option value="20">20 Years</option>
                            <option value="25">25 Years</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1 text-neutral-700 dark:text-slate-200">Description / Life Thesis</label>
                        <textarea name="description" rows="2" placeholder="e.g. Take up freelance consulting, cut dining out, invest heavily in index funds." class="glass-input w-full text-xs"></textarea>
                    </div>

                    <div class="pt-3 border-t border-neutral-200 dark:border-white/10 flex justify-end gap-2">
                        <button type="button" @click="showForkModal = false" class="px-4 py-2 rounded-xl text-neutral-400 hover:text-white">Cancel</button>
                        <button type="submit" class="btn-primary text-xs py-2 px-4 bg-purple-600 hover:bg-purple-500 shadow-purple-500/20 font-bold">
                            🌿 Fork Branch & Simulate
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Chart.js & Multiverse Client Engine --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    function multiverseApp() {
        return {
            showForkModal: false,
            userInput: '',
            isThinking: false,
            chatMessages: [],
            selectedId: {{ $selected->id }},

            init() {
                this.renderChart();
            },

            renderChart() {
                const ctx = document.getElementById('multiverseChart');
                if (!ctx) return;

                const mainData = @json(array_column($mainProj['years'], 'net_worth'));
                const branchData = @json(array_column($branchProj['years'], 'net_worth'));
                const fireData = @json(array_column($branchProj['years'], 'fire_target'));
                const labels = @json(array_map(fn($y) => "Year " . $y['year'] . " (Age " . $y['age'] . ")", $branchProj['years']));

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: '{{ $selected->slug }} (Branch)',
                                data: branchData,
                                borderColor: '#a855f7',
                                backgroundColor: 'rgba(168, 85, 247, 0.12)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointRadius: 2,
                            },
                            {
                                label: 'main (Status Quo)',
                                data: mainData,
                                borderColor: '#06b6d4',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [4, 4],
                                pointRadius: 1,
                                tension: 0.2,
                            },
                            {
                                label: 'FI/RE Target',
                                data: fireData,
                                borderColor: '#f59e0b',
                                backgroundColor: 'transparent',
                                borderWidth: 1.5,
                                borderDash: [6, 6],
                                pointRadius: 0,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#94a3b8', font: { size: 10 } }
                            },
                            y: {
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 10 },
                                    callback: function(v) {
                                        if (v >= 10000000) return 'Rs ' + (v / 10000000).toFixed(1) + 'Cr';
                                        if (v >= 100000) return 'Rs ' + (v / 100000).toFixed(0) + 'L';
                                        return 'Rs ' + v;
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(c) {
                                        const val = c.parsed.y || 0;
                                        const formatted = val >= 10000000 ? (val / 10000000).toFixed(2) + ' Cr' : (val / 100000).toFixed(1) + ' L';
                                        return c.dataset.label + ': Rs ' + formatted;
                                    }
                                }
                            }
                        }
                    }
                });
            },

            askFutureSelf(prompt) {
                this.userInput = prompt;
                this.submitChat();
            },

            async submitChat() {
                const text = this.userInput.trim();
                if (!text || this.isThinking) return;

                this.chatMessages.push({ role: 'user', text: text });
                this.userInput = '';
                this.isThinking = true;

                // Auto-scroll chat
                this.$nextTick(() => {
                    const el = document.getElementById('chatStream');
                    if (el) el.scrollTop = el.scrollHeight;
                });

                try {
                    const res = await fetch(`/api/multiverse/${this.selectedId}/future-self-chat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: text })
                    });

                    const data = await res.json();
                    if (data.reply) {
                        this.chatMessages.push({ role: 'bot', text: data.reply });
                    } else {
                        this.chatMessages.push({ role: 'bot', text: "Telemetry link interrupted. Compounding continues smoothly." });
                    }
                } catch (e) {
                    this.chatMessages.push({ role: 'bot', text: "Link timeout. In our timeline, staying the course was the best move." });
                } finally {
                    this.isThinking = false;
                    this.$nextTick(() => {
                        const el = document.getElementById('chatStream');
                        if (el) el.scrollTop = el.scrollHeight;
                    });
                }
            }
        };
    }
    </script>
</x-app-layout>
