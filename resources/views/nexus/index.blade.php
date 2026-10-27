<x-app-layout>
    <x-slot name="header">
        <div x-data class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl animate-pulse">⚡</span>
                    <h2 class="font-extrabold text-xl text-neutral-900 dark:text-white tracking-tight">
                        The Zen & Quant Nexus
                    </h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                        Behavioral × Quantitative × ROI
                    </span>
                </div>
                <p class="text-xs text-neutral-500 dark:text-slate-400 mt-1">
                    Real-time financial independence engine, multi-perspective council deliberation, 4-D capital allocation radar, and automated overnight cash sweep.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button @click="$dispatch('toggle-physics-tuner')"
                        type="button"
                        class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5 cursor-pointer">
                    <span>⚙️</span>
                    <span>Adjust Parameters</span>
                </button>
                <a href="#war-room-anchor"
                   @click="document.getElementById('proposalItemInput')?.focus()"
                   class="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 shadow-cyan-500/20 bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 cursor-pointer">
                    <span>⚔️</span>
                    <span>Summon Council</span>
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Pre-load Chart.js from CDN before component initialization --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div x-data="zenQuantNexus({
            velocity: {{ Js::from($velocity) }},
            kakeibo: {{ Js::from($kakeibo) }},
            yuebao: {{ Js::from($yuebao) }},
            recentDeliberations: {{ Js::from($recentDeliberations) }}
         })"
         @toggle-physics-tuner.window="showTuner = !showTuner"
         class="py-6 space-y-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Flash notification / status --}}
        <div x-show="bannerMessage" x-transition class="p-3.5 rounded-xl border border-cyan-500/30 bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-base">✨</span>
                <span x-text="bannerMessage"></span>
            </div>
            <button @click="bannerMessage = ''" class="text-neutral-400 hover:text-white">&times;</button>
        </div>

        {{-- ========================================================================= --}}
        {{-- MODULE 1: REAL-TIME MILLISECOND FINANCIAL ESCAPE VELOCITY TICKER         --}}
        {{-- ========================================================================= --}}
        <div class="relative glass-card p-6 md:p-8 border border-cyan-500/30 overflow-hidden shadow-2xl shadow-cyan-950/20">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                {{-- Top status row --}}
                <div class="flex items-center justify-between flex-wrap gap-4 border-b border-neutral-200/50 dark:border-white/10 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl flex items-center justify-center font-bold text-lg"
                             :class="velocity.is_escape ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30'">
                            <span x-text="velocity.is_escape ? '🎯' : '📈'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400">
                                Financial Independence Engine • Live Telemetry
                            </span>
                            <h3 class="text-lg font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                                <span x-text="velocity.is_escape ? 'Financial Independence Achieved' : 'Capital Accumulation Phase'"></span>
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
                                          :class="velocity.is_escape ? 'bg-emerald-400' : 'bg-cyan-400'"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5"
                                          :class="velocity.is_escape ? 'bg-emerald-500' : 'bg-cyan-500'"></span>
                                </span>
                            </h3>
                        </div>
                    </div>

                    {{-- Live Net Cash Flow Pill --}}
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 px-4 py-2 rounded-xl backdrop-blur-md"
                             :class="velocity.is_escape ? 'bg-emerald-500/10 border border-emerald-500/30 text-emerald-400' : 'bg-cyan-500/10 border border-cyan-500/30 text-cyan-400'">
                            <span class="text-xs font-semibold uppercase tracking-wider">Net Cash Flow:</span>
                            <span class="text-base font-mono font-extrabold"
                                  x-text="(velocity.net_velocity >= 0 ? '+' : '') + '₹' + Math.abs(velocity.net_velocity).toFixed(4) + '/sec'">
                            </span>
                        </div>

                        <button @click="showTuner = !showTuner" type="button"
                                class="px-3 py-2 rounded-xl bg-neutral-200/60 dark:bg-white/10 hover:bg-neutral-300 dark:hover:bg-white/20 text-xs font-semibold text-neutral-700 dark:text-slate-200 transition">
                            <span x-text="showTuner ? 'Close Simulator' : '⚙️ Simulator'"></span>
                        </button>
                    </div>
                </div>

                {{-- Interactive Parameter Tuning Simulator (Collapsible Drawer) --}}
                <div x-show="showTuner" x-transition class="p-5 rounded-2xl bg-neutral-900/90 dark:bg-black/60 border border-cyan-500/30 space-y-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-2">
                        <div class="flex items-center gap-2 text-cyan-400 font-bold text-xs uppercase tracking-wider">
                            <span>⚙️</span>
                            <span>Financial Independence Simulator & Parameter Tuning</span>
                        </div>
                        <span class="text-[10px] text-slate-400">Simulate changes to living expenses & investments in real time</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">
                                Monthly Living Expenses: <strong class="text-cyan-400 font-mono" x-text="'₹' + Number(tuneForm.burn).toLocaleString('en-IN')"></strong>
                            </label>
                            <input type="range" min="15000" max="150000" step="1000" x-model.number="tuneForm.burn" @input="recalcPhysics()"
                                   class="w-full accent-cyan-500 bg-neutral-800 rounded-lg cursor-pointer">
                        </div>

                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">
                                Invested Portfolio: <strong class="text-emerald-400 font-mono" x-text="'₹' + Number(tuneForm.assets).toLocaleString('en-IN')"></strong>
                            </label>
                            <input type="range" min="50000" max="10000000" step="25000" x-model.number="tuneForm.assets" @input="recalcPhysics()"
                                   class="w-full accent-emerald-500 bg-neutral-800 rounded-lg cursor-pointer">
                        </div>

                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">
                                Expected Return (CAGR): <strong class="text-purple-400 font-mono" x-text="tuneForm.cagr + '%'"></strong>
                            </label>
                            <input type="range" min="6" max="24" step="0.5" x-model.number="tuneForm.cagr" @input="recalcPhysics()"
                                   class="w-full accent-purple-500 bg-neutral-800 rounded-lg cursor-pointer">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-white/5">
                        <button type="button" @click="resetPhysics()"
                                class="text-[11px] text-slate-400 hover:text-white px-3 py-1">
                            Reset to Actual Database Values
                        </button>
                    </div>
                </div>

                {{-- Live Millisecond Odometer Ticker --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    {{-- Odometer Display --}}
                    <div class="md:col-span-2 bg-neutral-900/90 dark:bg-black/50 border border-cyan-500/20 rounded-2xl p-6 relative overflow-hidden">
                        <div class="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
                            <span>SESSION ACCUMULATED CASH FLOW</span>
                            <span class="flex items-center gap-1.5 text-cyan-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                                REAL-TIME ACCRUAL
                            </span>
                        </div>

                        <div class="flex items-baseline gap-3 flex-wrap">
                            <span id="odometerDisplay"
                                  class="text-3xl md:text-5xl font-extrabold font-mono tracking-tight text-emerald-400">
                                +₹0.0000
                            </span>
                            <span class="text-xs text-slate-400 font-mono">accumulated during this active session</span>
                        </div>

                        <p class="text-xs text-slate-400 mt-3 leading-relaxed">
                            <span class="text-slate-300 font-semibold" x-text="velocity.is_escape ? 'Self-Sustaining Portfolio:' : 'Monthly Cash Flow:'"></span>
                            Your invested portfolio yields
                            <strong class="text-emerald-400 font-mono" x-text="'₹' + velocity.yield_per_sec.toFixed(4) + '/sec'"></strong>
                            (₹<span x-text="Number(Math.round((velocity.invested_assets * (tuneForm.cagr/100)) / 12)).toLocaleString('en-IN')"></span>/mo at <span x-text="tuneForm.cagr"></span>% CAGR)
                            against monthly living expenses of
                            <strong class="text-rose-400 font-mono" x-text="'₹' + velocity.burn_per_sec.toFixed(4) + '/sec'"></strong>
                            (₹<span x-text="Number(velocity.monthly_burn).toLocaleString('en-IN')"></span>/mo).
                            <span x-show="!velocity.is_escape">
                                An additional <strong class="text-amber-400 font-mono" x-text="'₹' + Number(velocity.capital_gap).toLocaleString('en-IN')"></strong> in invested capital is needed for passive yields to fully cover living expenses.
                            </span>
                            <span x-show="velocity.is_escape">
                                Your portfolio is fully self-sustaining! Investment earnings exceed your monthly living expenses.
                            </span>
                        </p>
                    </div>

                    {{-- Financial Independence Progress --}}
                    <div class="bg-neutral-900/60 dark:bg-black/40 border border-white/10 rounded-2xl p-5 flex flex-col justify-between h-full space-y-4">
                        <div>
                            <div class="flex justify-between items-center text-xs font-semibold mb-1">
                                <span class="text-slate-400">Financial Independence Progress</span>
                                <span class="text-cyan-400 font-mono font-bold" x-text="velocity.escape_percent + '%'"></span>
                            </div>
                            <div class="w-full bg-neutral-800 rounded-full h-3 overflow-hidden border border-white/10 p-0.5">
                                <div class="h-full rounded-full bg-gradient-to-r from-cyan-500 via-indigo-500 to-emerald-400 transition-all duration-300"
                                     :style="`width: ${Math.min(100, velocity.escape_percent)}%`"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-white/5 text-[11px] font-mono">
                            <div>
                                <span class="text-slate-500 block">Current Portfolio</span>
                                <span class="font-bold text-slate-200" x-text="'₹' + Number(velocity.invested_assets).toLocaleString('en-IN')"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block">Independence Target</span>
                                <span class="font-bold text-cyan-400" x-text="'₹' + Number(velocity.capital_for_escape).toLocaleString('en-IN')"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- MODULE 2: THE TRI-AGENT ASIAN FINANCIAL WAR ROOM                          --}}
        {{-- ========================================================================= --}}
        <div id="war-room-anchor" class="glass-card p-6 md:p-8 space-y-6 border border-indigo-500/20 relative">
            <div class="flex items-center justify-between flex-wrap gap-4 border-b border-neutral-200/50 dark:border-white/10 pb-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xl">⚔️</span>
                        <h3 class="text-lg font-bold text-neutral-900 dark:text-white tracking-tight">
                            The Multi-Perspective Financial Council
                        </h3>
                    </div>
                    <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">
                        Deliberate any planned purchase proposal through Behavioral Utility, Quantitative Compounding, and Career Growth ROI.
                    </p>
                </div>

                {{-- Presets pills --}}
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="text-neutral-400 dark:text-slate-500 font-semibold text-[10px] uppercase">1-Click Presets:</span>
                    <button type="button" @click="applyPreset('Sony WH-1000XM5 Headphones', 24990, 'Gadgets', 'Noise cancellation for deep focus coding sprints')"
                            class="px-2.5 py-1 rounded-lg bg-neutral-200/60 dark:bg-white/5 hover:bg-neutral-300 dark:hover:bg-white/10 text-neutral-700 dark:text-slate-300 transition text-[11px]">
                        🎧 ANC Headphones
                    </button>
                    <button type="button" @click="applyPreset('Omakase Fine Dining with Friends', 8500, 'Food & Dining', 'Weekend celebration with close colleagues')"
                            class="px-2.5 py-1 rounded-lg bg-neutral-200/60 dark:bg-white/5 hover:bg-neutral-300 dark:hover:bg-white/10 text-neutral-700 dark:text-slate-300 transition text-[11px]">
                        🍣 Omakase Dinner
                    </button>
                    <button type="button" @click="applyPreset('AI & Cloud Architecture Masterclass', 16000, 'Education', 'Certification to negotiate 30% salary hike')"
                            class="px-2.5 py-1 rounded-lg bg-neutral-200/60 dark:bg-white/5 hover:bg-neutral-300 dark:hover:bg-white/10 text-neutral-700 dark:text-slate-300 transition text-[11px]">
                        📚 AI Masterclass
                    </button>
                    <button type="button" @click="applyPreset('Latest Flagship Smartphone', 129990, 'Electronics', 'Upgrade from 3-year-old phone for camera and battery')"
                            class="px-2.5 py-1 rounded-lg bg-neutral-200/60 dark:bg-white/5 hover:bg-neutral-300 dark:hover:bg-white/10 text-neutral-700 dark:text-slate-300 transition text-[11px]">
                        📱 Flagship Phone
                    </button>
                </div>
            </div>

            {{-- Input Form --}}
            <form @submit.prevent="summonWarRoom()" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-slate-300 mb-1">
                        Item / Purchase Proposal
                    </label>
                    <input id="proposalItemInput" type="text" x-model="warRoomForm.item_name" required placeholder="e.g. Ergonomic Office Chair"
                           class="w-full glass-input text-sm rounded-xl px-3.5 py-2.5 border border-neutral-300 dark:border-white/10 focus:border-cyan-500 dark:bg-black/40 text-neutral-900 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-slate-300 mb-1">
                        Amount (₹)
                    </label>
                    <input type="number" x-model.number="warRoomForm.amount" required min="1" step="any" placeholder="18500"
                           class="w-full glass-input text-sm rounded-xl px-3.5 py-2.5 border border-neutral-300 dark:border-white/10 focus:border-cyan-500 dark:bg-black/40 text-neutral-900 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-slate-300 mb-1">
                        Category
                    </label>
                    <select x-model="warRoomForm.category"
                            class="w-full glass-input text-sm rounded-xl px-3.5 py-2.5 border border-neutral-300 dark:border-white/10 focus:border-cyan-500 dark:bg-black/40 text-neutral-900 dark:text-white">
                        <option value="Shopping">Shopping</option>
                        <option value="Gadgets">Gadgets</option>
                        <option value="Food & Dining">Food & Dining</option>
                        <option value="Education">Education</option>
                        <option value="Fitness">Fitness</option>
                        <option value="Travel">Travel</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-neutral-700 dark:text-slate-300 mb-1">
                        User Justification
                    </label>
                    <div class="flex gap-2">
                        <input type="text" x-model="warRoomForm.reason" placeholder="Why do you want to make this purchase?"
                               class="flex-1 glass-input text-sm rounded-xl px-3.5 py-2.5 border border-neutral-300 dark:border-white/10 focus:border-cyan-500 dark:bg-black/40 text-neutral-900 dark:text-white">
                        <button type="submit" :disabled="isDeliberating"
                                class="btn-primary px-4 py-2.5 text-xs font-bold flex items-center justify-center gap-1.5 min-w-[130px] bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-500 hover:to-pink-500 disabled:opacity-50 cursor-pointer">
                            <span x-show="!isDeliberating">Summon Council</span>
                            <span x-show="isDeliberating" class="flex items-center gap-1">
                                <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Debating...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Loading Scanner Hologram when Council is Convening --}}
            <div x-show="isDeliberating" x-transition class="p-8 rounded-2xl bg-neutral-900/90 dark:bg-black/60 border border-indigo-500/40 text-center space-y-3">
                <div class="inline-flex p-3 rounded-full bg-indigo-500/20 text-indigo-400 animate-pulse text-2xl">
                    ⚔️
                </div>
                <h4 class="text-sm font-bold text-white tracking-wide">
                    Autonomous Multi-Perspective Audit in Progress...
                </h4>
                <p class="text-xs text-slate-400 max-w-md mx-auto">
                    Analyzing behavioral utility, 10-year compounding opportunity cost, and productivity leverage...
                </p>
                <div class="w-48 mx-auto bg-neutral-800 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-500 h-full rounded-full animate-pulse w-full"></div>
                </div>
            </div>

            {{-- Active Deliberation Chamber Display --}}
            <div x-show="activeDeliberation && !isDeliberating" x-transition class="space-y-6 pt-4 border-t border-neutral-200/50 dark:border-white/10">
                {{-- Header with Item name and Opportunity Cost --}}
                <div class="flex items-center justify-between flex-wrap gap-4 bg-neutral-900/80 dark:bg-black/50 p-4 rounded-2xl border border-white/10">
                    <div>
                        <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400">Proposal Under Review</span>
                        <h4 class="text-lg font-bold text-white flex items-center gap-2">
                            <span x-text="activeDeliberation?.item_name"></span>
                            <span class="text-cyan-400 font-mono text-sm" x-text="'(₹' + Number(activeDeliberation?.amount || 0).toLocaleString('en-IN') + ')'"></span>
                        </h4>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-mono tracking-wider text-rose-400 block">10-Yr Compounding Opp Cost</span>
                            <span class="text-sm font-mono font-extrabold text-rose-300" x-text="activeDeliberation?.opp_cost_10y || 'Calculating...'"></span>
                        </div>

                        {{-- Vote Badge --}}
                        <div class="px-3.5 py-1.5 rounded-xl font-bold uppercase tracking-wider text-xs flex items-center gap-1.5 shadow-lg"
                             :class="{
                                'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40': activeDeliberation?.vote === 'approved',
                                'bg-rose-500/20 text-rose-400 border border-rose-500/40': activeDeliberation?.vote === 'vetoed',
                                'bg-amber-500/20 text-amber-400 border border-amber-500/40': activeDeliberation?.vote === 'conditional'
                             }">
                            <span x-text="activeDeliberation?.vote === 'approved' ? '🛡️' : (activeDeliberation?.vote === 'vetoed' ? '⛔' : '⚖️')"></span>
                            <span x-text="'COUNCIL ' + (activeDeliberation?.vote || '').toUpperCase()"></span>
                        </div>
                    </div>
                </div>

                {{-- The 3 Analytical Dimension Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- 1. Behavioral & Mindful Audit --}}
                    <div class="glass-card p-5 border border-emerald-500/30 relative overflow-hidden bg-gradient-to-b from-emerald-950/20 to-transparent">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-xl shadow-md">
                                🧠
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-neutral-900 dark:text-emerald-300">Behavioral & Mindful Audit</h5>
                                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 uppercase tracking-wider font-semibold">Spending Utility & Cognitive Impact</p>
                            </div>
                        </div>
                        <p class="text-xs text-neutral-700 dark:text-slate-300 leading-relaxed italic border-l-2 border-emerald-500/40 pl-3 py-1"
                           x-text="activeDeliberation?.kenji">
                        </p>
                    </div>

                    {{-- 2. Quantitative Opportunity Cost --}}
                    <div class="glass-card p-5 border border-cyan-500/30 relative overflow-hidden bg-gradient-to-b from-cyan-950/20 to-transparent">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-cyan-500/20 border border-cyan-500/30 flex items-center justify-center text-xl shadow-md">
                                ⚡
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-neutral-900 dark:text-cyan-300">Quantitative Opportunity Cost</h5>
                                <p class="text-[10px] text-cyan-600 dark:text-cyan-400 uppercase tracking-wider font-semibold">10-Yr Compounding & Capital Velocity</p>
                            </div>
                        </div>
                        <p class="text-xs text-neutral-700 dark:text-slate-300 leading-relaxed italic border-l-2 border-cyan-500/40 pl-3 py-1"
                           x-text="activeDeliberation?.chen">
                        </p>
                    </div>

                    {{-- 3. Productivity & Growth ROI --}}
                    <div class="glass-card p-5 border border-purple-500/30 relative overflow-hidden bg-gradient-to-b from-purple-950/20 to-transparent">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-xl shadow-md">
                                🚀
                            </div>
                            <div>
                                <h5 class="text-sm font-bold text-neutral-900 dark:text-purple-300">Productivity & Growth ROI</h5>
                                <p class="text-[10px] text-purple-600 dark:text-purple-400 uppercase tracking-wider font-semibold">Time Leverage & Career Velocity</p>
                            </div>
                        </div>
                        <p class="text-xs text-neutral-700 dark:text-slate-300 leading-relaxed italic border-l-2 border-purple-500/40 pl-3 py-1"
                           x-text="activeDeliberation?.minjun">
                        </p>
                    </div>
                </div>

                {{-- Final Ruling Banner --}}
                <div class="p-4 rounded-xl border border-white/10 bg-neutral-900/60 dark:bg-black/40 flex items-start gap-3">
                    <span class="text-lg">📜</span>
                    <div>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400">Final Council Ruling & Protocol</span>
                        <p class="text-xs font-semibold text-neutral-900 dark:text-slate-200 mt-0.5 leading-relaxed"
                           x-text="activeDeliberation?.ruling">
                        </p>
                    </div>
                </div>
            </div>

            {{-- Recent Deliberations Archive --}}
            <div x-show="recentList && recentList.length > 0" class="pt-4 border-t border-neutral-200/50 dark:border-white/10">
                <span class="text-[11px] font-semibold text-neutral-500 dark:text-slate-400 uppercase tracking-wider block mb-3">
                    Recent Council Deliberations Archive:
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="item in recentList" :key="item.id">
                        <div @click="loadDeliberation(item)"
                             class="p-3 rounded-xl border border-white/5 bg-neutral-100/50 dark:bg-white/5 hover:border-cyan-500/40 cursor-pointer transition flex items-center justify-between">
                            <div>
                                <h6 class="text-xs font-bold text-neutral-900 dark:text-white" x-text="item.item_name"></h6>
                                <p class="text-[10px] text-neutral-500 dark:text-slate-400 font-mono" x-text="'₹' + Number(item.amount).toLocaleString('en-IN')"></p>
                            </div>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full"
                                  :class="{
                                      'bg-emerald-500/20 text-emerald-400': item.council_vote === 'approved',
                                      'bg-rose-500/20 text-rose-400': item.council_vote === 'vetoed',
                                      'bg-amber-500/20 text-amber-400': item.council_vote === 'conditional'
                                  }"
                                  x-text="item.council_vote">
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- MODULE 3 & 4: 4-D CAPITAL RADAR & AUTOMATED CASH SWEEP                     --}}
        {{-- ========================================================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Module 3: 4-D Capital Allocation Radar (7 cols) --}}
            <div class="lg:col-span-7 glass-card p-6 md:p-8 space-y-6 border border-cyan-500/20 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-4 border-b border-neutral-200/50 dark:border-white/10 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🕸️</span>
                            <div>
                                <h4 class="text-base font-bold text-neutral-900 dark:text-white">
                                    The 4-D Capital Allocation Radar
                                </h4>
                                <p class="text-xs text-neutral-500 dark:text-slate-400">
                                    4-axis spending analysis & lifestyle creep detector
                                </p>
                            </div>
                        </div>

                        {{-- Dopamine Audit Badge --}}
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold border"
                              :class="kakeibo.audit_class"
                              x-text="kakeibo.audit_badge">
                        </span>
                    </div>

                    {{-- Chart & Ratios --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        {{-- Radar Chart Container with fixed bounds --}}
                        <div class="md:col-span-7 h-[250px] relative flex items-center justify-center">
                            <canvas id="kakeiboRadarChart" style="max-height: 240px;"></canvas>
                        </div>

                        {{-- Quadrant Breakdown Bars --}}
                        <div class="md:col-span-5 space-y-3 text-xs">
                            {{-- Physical Essentials --}}
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-neutral-600 dark:text-slate-400">Physical Essentials</span>
                                    <span class="font-bold text-cyan-400" x-text="'₹' + Number(kakeibo.survival).toLocaleString('en-IN') + ' (' + kakeibo.survival_pct + '%)'"></span>
                                </div>
                                <div class="w-full bg-neutral-200 dark:bg-neutral-800 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-cyan-400 h-full rounded-full transition-all duration-500" :style="`width: ${kakeibo.survival_pct}%`"></div>
                                </div>
                            </div>

                            {{-- Growth & Development --}}
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-neutral-600 dark:text-slate-400">Growth & Development</span>
                                    <span class="font-bold text-emerald-400" x-text="'₹' + Number(kakeibo.soul).toLocaleString('en-IN') + ' (' + kakeibo.soul_pct + '%)'"></span>
                                </div>
                                <div class="w-full bg-neutral-200 dark:bg-neutral-800 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-400 h-full rounded-full transition-all duration-500" :style="`width: ${kakeibo.soul_pct}%`"></div>
                                </div>
                            </div>

                            {{-- Discretionary Leisure --}}
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-neutral-600 dark:text-slate-400">Discretionary Leisure</span>
                                    <span class="font-bold text-rose-400" x-text="'₹' + Number(kakeibo.hedonic).toLocaleString('en-IN') + ' (' + kakeibo.hedonic_pct + '%)'"></span>
                                </div>
                                <div class="w-full bg-neutral-200 dark:bg-neutral-800 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-rose-400 h-full rounded-full transition-all duration-500" :style="`width: ${kakeibo.hedonic_pct}%`"></div>
                                </div>
                            </div>

                            {{-- Unplanned Miscellaneous --}}
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-neutral-600 dark:text-slate-400">Unplanned Miscellaneous</span>
                                    <span class="font-bold text-amber-400" x-text="'₹' + Number(kakeibo.chaos).toLocaleString('en-IN') + ' (' + kakeibo.chaos_pct + '%)'"></span>
                                </div>
                                <div class="w-full bg-neutral-200 dark:bg-neutral-800 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-amber-400 h-full rounded-full transition-all duration-500" :style="`width: ${kakeibo.chaos_pct}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- AI Dopamine Audit Verdict Card --}}
                <div class="p-4 rounded-xl border border-white/10 bg-neutral-900/60 dark:bg-black/40 text-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Discretionary to Growth Ratio</span>
                        <span class="font-mono font-extrabold text-sm"
                              :class="kakeibo.hedonic_ratio > 2.0 ? 'text-rose-400' : 'text-emerald-400'"
                              x-text="kakeibo.hedonic_ratio + 'x'">
                        </span>
                    </div>
                    <p class="text-neutral-300 dark:text-slate-300 leading-relaxed" x-text="kakeibo.audit_verdict"></p>
                </div>
            </div>

            {{-- Module 4: Autonomous Cash Sweep & Arbitrageur (5 cols) --}}
            <div class="lg:col-span-5 glass-card p-6 md:p-8 space-y-6 border border-emerald-500/20 relative flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-4 border-b border-neutral-200/50 dark:border-white/10 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🌙</span>
                            <div>
                                <h4 class="text-base font-bold text-neutral-900 dark:text-white">
                                    Automated Overnight Cash Sweep
                                </h4>
                                <p class="text-xs text-neutral-500 dark:text-slate-400">
                                    Automated liquid cash yield capture engine
                                </p>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                            +4.5% Spread
                        </span>
                    </div>

                    <p class="text-xs text-neutral-600 dark:text-slate-300 leading-relaxed mb-4">
                        Capital sitting in low-yield checking accounts suffers purchasing power loss. The automated sweep engine transfers idle cash balances above your 30-day operating buffer into AAA overnight liquid funds.
                    </p>

                    {{-- Arbitrage Stats Grid --}}
                    <div class="space-y-3">
                        <div class="bg-neutral-900/80 dark:bg-black/50 p-3.5 rounded-xl border border-white/10 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400 block">Idle Cash Above Buffer</span>
                                <span class="text-base font-mono font-bold text-white" x-text="'₹' + Number(yuebao.idle_cash).toLocaleString('en-IN')"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400 block">30-Day Buffer Kept</span>
                                <span class="text-xs font-mono text-slate-300" x-text="'₹' + Number(yuebao.operating_buffer).toLocaleString('en-IN')"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-neutral-900/60 dark:bg-black/30 border border-white/5">
                                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400 block">Daily Sweep Gain</span>
                                <span class="text-sm font-mono font-bold text-emerald-400" x-text="'+₹' + Number(yuebao.daily_arbitrage).toFixed(2) + '/day'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-neutral-900/60 dark:bg-black/30 border border-white/5">
                                <span class="text-[10px] uppercase font-mono tracking-wider text-slate-400 block">Monthly Passive Yield</span>
                                <span class="text-sm font-mono font-bold text-cyan-400" x-text="'+₹' + Number(yuebao.monthly_arbitrage).toLocaleString('en-IN') + '/mo'"></span>
                            </div>
                        </div>

                        {{-- 10-Year Compounding Opportunity Cost --}}
                        <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-950/30 to-cyan-950/30 border border-emerald-500/20 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] uppercase tracking-wider font-mono text-slate-400">10-Year Compounded Delta</span>
                                <span class="text-xs font-bold text-emerald-400 font-mono" x-text="'+₹' + Number(yuebao.ten_year_compound).toLocaleString('en-IN')"></span>
                            </div>
                            <p class="text-[11px] text-slate-400 leading-relaxed">
                                Simply sweeping your ₹<span x-text="Number(yuebao.idle_cash).toLocaleString('en-IN')"></span> idle cash into 7.2% liquid fund yields ₹<span x-text="Number(yuebao.ten_year_compound).toLocaleString('en-IN')"></span> more than keeping it in a standard bank savings account over 10 years.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action button --}}
                <div class="pt-4 border-t border-neutral-200/50 dark:border-white/10">
                    <button type="button" @click="toggleSweepPlan()"
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-bold flex items-center justify-center gap-2 text-white shadow-lg shadow-emerald-950/30 transition cursor-pointer"
                            :class="isSweepActive ? 'bg-gradient-to-r from-teal-700 to-emerald-700 ring-2 ring-emerald-400' : 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500'">
                        <span x-text="isSweepActive ? '✅' : '⚡'"></span>
                        <span x-text="isSweepActive ? 'Automated Sweep Active (+₹' + yuebao.daily_arbitrage + '/day) • Click to Toggle' : 'Simulate 1-Click Overnight Sweep'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
    function zenQuantNexus(data) {
        return {
            velocity: data.velocity,
            kakeibo: data.kakeibo,
            yuebao: data.yuebao,
            recentList: data.recentDeliberations || [],
            activeDeliberation: data.recentDeliberations && data.recentDeliberations.length > 0 ? {
                item_name: data.recentDeliberations[0].item_name,
                amount: data.recentDeliberations[0].amount,
                kenji: data.recentDeliberations[0].kenji_argument,
                chen: data.recentDeliberations[0].chen_argument,
                minjun: data.recentDeliberations[0].minjun_argument,
                vote: data.recentDeliberations[0].council_vote,
                ruling: data.recentDeliberations[0].final_ruling,
                opp_cost_10y: '₹' + Number(data.recentDeliberations[0].amount * Math.pow(1 + 0.12, 10)).toLocaleString('en-IN', {maximumFractionDigits: 0})
            } : {
                item_name: 'Sony WH-1000XM5 Headphones',
                amount: 24990,
                kenji: 'Examine whether this item fosters authentic life clarity or introduces cognitive clutter and operational friction. Prioritize high-utility tools over impulse dopamine.',
                chen: 'The 10-year compounding opportunity cost in Nifty 50 at 12% CAGR is ₹77,615. Tolerable only if leveraged directly to generate productive revenue or career upside.',
                minjun: 'In high-intensity knowledge work, audio isolation protects up to 2 extra deep-work hours daily. The productivity ROI is positive if deployed for career acceleration.',
                vote: 'conditional',
                ruling: 'CONDITIONAL: Proceed only if you match ₹5,000 into your liquid investment fund today.',
                opp_cost_10y: '₹77,615'
            },

            bannerMessage: '',
            showTuner: false,
            tuneForm: {
                burn: data.velocity.monthly_burn,
                assets: data.velocity.invested_assets,
                cagr: data.velocity.cagr_pct || 12
            },
            originalVelocity: JSON.parse(JSON.stringify(data.velocity)),

            startTime: Date.now(),
            animFrameId: null,
            isSweepActive: false,

            warRoomForm: {
                item_name: '',
                amount: '',
                category: 'Shopping',
                reason: ''
            },
            isDeliberating: false,
            radarChartInstance: null,

            init() {
                this.startOdometer();
                this.initKakeiboRadar();
            },

            startOdometer() {
                const el = document.getElementById('odometerDisplay');
                const step = () => {
                    const elapsedSeconds = (Date.now() - this.startTime) / 1000;
                    const sessionDelta = elapsedSeconds * this.velocity.net_velocity;
                    if (el) {
                        el.textContent = (sessionDelta >= 0 ? '+' : '-') + '₹' + Math.abs(sessionDelta).toFixed(4);
                        if (sessionDelta >= 0) {
                            el.className = 'text-3xl md:text-5xl font-extrabold font-mono tracking-tight text-emerald-400';
                        } else {
                            el.className = 'text-3xl md:text-5xl font-extrabold font-mono tracking-tight text-rose-400';
                        }
                    }
                    this.animFrameId = requestAnimationFrame(step);
                };
                this.animFrameId = requestAnimationFrame(step);
            },

            recalcPhysics() {
                const burn = Number(this.tuneForm.burn);
                const assets = Number(this.tuneForm.assets);
                const cagr = Number(this.tuneForm.cagr) / 100;

                const burnPerSec = burn / 2592000;
                const yieldPerSec = (assets * cagr) / 31536000;
                const netVelocity = yieldPerSec - burnPerSec;

                const capitalForEscape = (burn * 12) / cagr;
                const capitalGap = Math.max(0, capitalForEscape - assets);
                const escapePercent = Math.min(100, Math.round((assets / Math.max(1, capitalForEscape)) * 100));

                this.velocity.monthly_burn = burn;
                this.velocity.invested_assets = assets;
                this.velocity.burn_per_sec = burnPerSec;
                this.velocity.yield_per_sec = yieldPerSec;
                this.velocity.net_velocity = netVelocity;
                this.velocity.is_escape = netVelocity >= 0;
                this.velocity.capital_for_escape = capitalForEscape;
                this.velocity.capital_gap = capitalGap;
                this.velocity.escape_percent = escapePercent;

                // Reset session time so odometer builds from zero on new velocity
                this.startTime = Date.now();
            },

            resetPhysics() {
                this.tuneForm.burn = this.originalVelocity.monthly_burn;
                this.tuneForm.assets = this.originalVelocity.invested_assets;
                this.tuneForm.cagr = this.originalVelocity.cagr_pct || 12;
                this.recalcPhysics();
            },

            applyPreset(item, amt, cat, reason) {
                this.warRoomForm.item_name = item;
                this.warRoomForm.amount = amt;
                this.warRoomForm.category = cat;
                this.warRoomForm.reason = reason;
                this.bannerMessage = `Preset applied for "${item}". Click "Summon Council" to deliberate!`;
            },

            async summonWarRoom() {
                if (!this.warRoomForm.item_name || !this.warRoomForm.amount || this.isDeliberating) return;
                this.isDeliberating = true;
                this.bannerMessage = '';

                try {
                    const res = await fetch('{{ route('nexus.war-room') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.warRoomForm)
                    });

                    const json = await res.json();
                    if (json.success) {
                        this.activeDeliberation = json;
                        this.recentList.unshift({
                            id: json.deliberation_id || Date.now(),
                            item_name: json.item_name,
                            amount: json.amount,
                            council_vote: json.vote,
                            kenji_argument: json.kenji,
                            chen_argument: json.chen,
                            minjun_argument: json.minjun,
                            final_ruling: json.ruling
                        });
                        this.bannerMessage = `Council ruling delivered: ${json.vote.toUpperCase()} for "${json.item_name}".`;
                    } else {
                        this.bannerMessage = 'Council deliberation could not be completed. Please retry.';
                    }
                } catch (e) {
                    console.error('War Room deliberation error:', e);
                    this.bannerMessage = 'Deliberation request interrupted. Please check network connection.';
                } finally {
                    this.isDeliberating = false;
                }
            },

            loadDeliberation(item) {
                this.activeDeliberation = {
                    item_name: item.item_name,
                    amount: item.amount,
                    kenji: item.kenji_argument,
                    chen: item.chen_argument,
                    minjun: item.minjun_argument,
                    vote: item.council_vote,
                    ruling: item.final_ruling,
                    opp_cost_10y: '₹' + Number(item.amount * Math.pow(1 + 0.12, 10)).toLocaleString('en-IN', {maximumFractionDigits: 0})
                };
                this.bannerMessage = `Loaded deliberation archive for "${item.item_name}".`;
            },

            toggleSweepPlan() {
                const additionalYieldPerSec = (this.yuebao.annual_arbitrage) / 31536000;
                if (!this.isSweepActive) {
                    this.isSweepActive = true;
                    this.velocity.yield_per_sec += additionalYieldPerSec;
                    this.velocity.net_velocity += additionalYieldPerSec;
                    if (this.velocity.net_velocity >= 0) {
                        this.velocity.is_escape = true;
                    }
                    this.bannerMessage = `Overnight cash sweep activated! Idle cash ₹${Number(this.yuebao.idle_cash).toLocaleString('en-IN')} now yielding +₹${this.yuebao.daily_arbitrage}/day (+4.5% spread).`;
                } else {
                    this.isSweepActive = false;
                    this.velocity.yield_per_sec -= additionalYieldPerSec;
                    this.velocity.net_velocity -= additionalYieldPerSec;
                    this.velocity.is_escape = this.velocity.net_velocity >= 0;
                    this.bannerMessage = `Overnight cash sweep simulation deactivated.`;
                }
                this.startTime = Date.now();
            },

            initKakeiboRadar() {
                const ctx = document.getElementById('kakeiboRadarChart')?.getContext('2d');
                if (!ctx) return;

                if (typeof Chart === 'undefined') {
                    setTimeout(() => this.initKakeiboRadar(), 150);
                    return;
                }

                if (this.radarChartInstance) {
                    this.radarChartInstance.destroy();
                }

                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#94a3b8' : '#475569';
                const gridColor = isDark ? 'rgba(255, 255, 255, 0.12)' : 'rgba(0, 0, 0, 0.1)';

                this.radarChartInstance = new Chart(ctx, {
                    type: 'radar',
                    data: {
                        labels: [
                            'Physical Essentials',
                            'Growth & Development',
                            'Discretionary Leisure',
                            'Unplanned Miscellaneous'
                        ],
                        datasets: [{
                            label: 'Allocation %',
                            data: [
                                this.kakeibo.survival_pct,
                                this.kakeibo.soul_pct,
                                this.kakeibo.hedonic_pct,
                                this.kakeibo.chaos_pct
                            ],
                            backgroundColor: 'rgba(34, 211, 238, 0.25)',
                            borderColor: '#22d3ee',
                            pointBackgroundColor: '#38bdf8',
                            pointBorderColor: '#ffffff',
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#38bdf8',
                            pointRadius: 4,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ` ${ctx.label}: ${ctx.raw}%`
                                }
                            }
                        },
                        scales: {
                            r: {
                                angleLines: { color: gridColor },
                                grid: { color: gridColor },
                                pointLabels: {
                                    color: textColor,
                                    font: { size: 10, weight: 'bold' }
                                },
                                ticks: {
                                    display: false,
                                    backdropColor: 'transparent'
                                },
                                min: 0,
                                max: Math.max(50, Math.max(
                                    this.kakeibo.survival_pct,
                                    this.kakeibo.soul_pct,
                                    this.kakeibo.hedonic_pct,
                                    this.kakeibo.chaos_pct
                                ) + 10)
                            }
                        }
                    }
                });
            }
        };
    }
    </script>
</x-app-layout>
