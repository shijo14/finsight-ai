<x-app-layout>
    <div x-data="timeMachineSimulator({
        initialCash: {{ $liquidCash }},
        initialBurn: {{ $monthlyBurn }},
        initialIncome: {{ $monthlyIncome }},
        initialInvested: {{ $investedAssets }},
        initialNetWorth: {{ $netWorth }}
    })" x-init="runSimulation()" class="py-6">

        <x-slot name="header">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">⏳</span>
                        <h2 class="font-bold text-xl text-neutral-900 dark:text-white leading-tight">
                            Financial Time Machine & Monte Carlo Simulator
                        </h2>
                    </div>
                    <p class="text-xs text-neutral-500 dark:text-slate-400 mt-1">
                        Run 1,000 stochastic financial simulations to stress-test your life decisions, career pivots, and market crashes.
                    </p>
                </div>

                {{-- Live Survival Badge --}}
                <div class="flex items-center gap-3 bg-neutral-100 dark:bg-white/5 border border-neutral-200 dark:border-white/10 px-4 py-2 rounded-2xl">
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-neutral-400 block">Survival Probability</span>
                        <span class="text-lg font-extrabold" 
                              :class="survivalRate >= 80 ? 'text-emerald-500' : (survivalRate >= 50 ? 'text-amber-500' : 'text-red-500')"
                              x-text="survivalRate + '%'">100%</span>
                    </div>
                    <div class="w-3 h-3 rounded-full animate-ping"
                         :class="survivalRate >= 80 ? 'bg-emerald-500' : (survivalRate >= 50 ? 'bg-amber-500' : 'bg-red-500')"></div>
                </div>
            </div>
        </x-slot>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Reality Baseline Strip --}}
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <div class="glass-card p-4">
                    <span class="text-[11px] font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider block">Liquid Cash</span>
                    <span class="text-xl font-bold text-cyan-600 dark:text-cyan-400 mt-1 block">Rs {{ number_format($liquidCash) }}</span>
                </div>
                <div class="glass-card p-4">
                    <span class="text-[11px] font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider block">Monthly Burn</span>
                    <span class="text-xl font-bold text-red-500 dark:text-red-400 mt-1 block">Rs {{ number_format($monthlyBurn) }}/mo</span>
                </div>
                <div class="glass-card p-4">
                    <span class="text-[11px] font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider block">Monthly Inflow</span>
                    <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1 block">Rs {{ number_format($monthlyIncome) }}/mo</span>
                </div>
                <div class="glass-card p-4">
                    <span class="text-[11px] font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider block">Invested Assets</span>
                    <span class="text-xl font-bold text-indigo-500 dark:text-indigo-400 mt-1 block">Rs {{ number_format($investedAssets) }}</span>
                </div>
                <div class="glass-card p-4">
                    <span class="text-[11px] font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider block">Total Net Worth</span>
                    <span class="text-xl font-bold text-neutral-900 dark:text-white mt-1 block">Rs {{ number_format($netWorth) }}</span>
                </div>
            </div>

            {{-- Crisis & Decision Presets --}}
            <div class="glass-card p-5">
                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-700 dark:text-slate-200">Simulate Radical Scenarios</h3>
                        <p class="text-xs text-neutral-500 dark:text-slate-400">Click a preset to stress-test that life event:</p>
                    </div>
                    <button @click="resetToBaseline()" type="button" class="btn-secondary text-xs py-1.5 px-3">
                        🔄 Reset Reality
                    </button>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <button @click="applyPreset('startup')" 
                            :class="activePreset === 'startup' ? 'border-cyan-400 ring-2 ring-cyan-400/20 bg-cyan-500/10' : 'hover:border-neutral-400 dark:hover:border-white/20'"
                            type="button" 
                            class="p-3.5 rounded-xl border border-neutral-200 dark:border-white/10 text-left transition-all glass-card-hover">
                        <div class="text-lg mb-1">🚀</div>
                        <div class="font-semibold text-xs text-neutral-900 dark:text-white">Startup Leap</div>
                        <div class="text-[11px] text-neutral-500 dark:text-slate-400 mt-0.5">Quit job, Rs 0 salary for 12 mos, trim burn by 20%.</div>
                    </button>

                    <button @click="applyPreset('crash')" 
                            :class="activePreset === 'crash' ? 'border-red-400 ring-2 ring-red-400/20 bg-red-500/10' : 'hover:border-neutral-400 dark:hover:border-white/20'"
                            type="button" 
                            class="p-3.5 rounded-xl border border-neutral-200 dark:border-white/10 text-left transition-all glass-card-hover">
                        <div class="text-lg mb-1">📉</div>
                        <div class="font-semibold text-xs text-neutral-900 dark:text-white">2008 Market Crash</div>
                        <div class="text-[11px] text-neutral-500 dark:text-slate-400 mt-0.5">-35% portfolio plunge, 8% inflation shock.</div>
                    </button>

                    <button @click="applyPreset('homeloan')" 
                            :class="activePreset === 'homeloan' ? 'border-amber-400 ring-2 ring-amber-400/20 bg-amber-500/10' : 'hover:border-neutral-400 dark:hover:border-white/20'"
                            type="button" 
                            class="p-3.5 rounded-xl border border-neutral-200 dark:border-white/10 text-left transition-all glass-card-hover">
                        <div class="text-lg mb-1">🏠</div>
                        <div class="font-semibold text-xs text-neutral-900 dark:text-white">Mega Home Purchase</div>
                        <div class="text-[11px] text-neutral-500 dark:text-slate-400 mt-0.5">Rs 15L down payment, Rs 45,000 monthly EMI.</div>
                    </button>

                    <button @click="applyPreset('inflation')" 
                            :class="activePreset === 'inflation' ? 'border-purple-400 ring-2 ring-purple-400/20 bg-purple-500/10' : 'hover:border-neutral-400 dark:hover:border-white/20'"
                            type="button" 
                            class="p-3.5 rounded-xl border border-neutral-200 dark:border-white/10 text-left transition-all glass-card-hover">
                        <div class="text-lg mb-1">💸</div>
                        <div class="font-semibold text-xs text-neutral-900 dark:text-white">Hyperinflation Shock</div>
                        <div class="text-[11px] text-neutral-500 dark:text-slate-400 mt-0.5">+15% cost of living jump with stagnant wages.</div>
                    </button>
                </div>
            </div>

            {{-- Main Simulation Workspace --}}
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Left: Interactive Sliders Console --}}
                <div class="glass-card p-6 space-y-5">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-neutral-900 dark:text-white flex items-center justify-between">
                        <span>Simulation Levers</span>
                        <span class="text-xs text-cyan-500 font-semibold" x-text="simMonths + ' Months'">36 Months</span>
                    </h3>

                    {{-- Slider 1: Monthly Income Modifier --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-neutral-600 dark:text-slate-300 font-medium">Income Modifier</span>
                            <span class="font-bold text-cyan-600 dark:text-cyan-400" x-text="(incomeMod >= 0 ? '+' : '') + incomeMod + '%'">+0%</span>
                        </div>
                        <input type="range" min="-100" max="100" step="5" x-model.number="incomeMod" @input="runSimulation()" class="w-full accent-cyan-500 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-neutral-400">
                            <span>-100% (Quit Job)</span>
                            <span>Baseline</span>
                            <span>+100% (Promotion)</span>
                        </div>
                    </div>

                    {{-- Slider 2: Monthly Burn Modifier --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-neutral-600 dark:text-slate-300 font-medium">Living Expenses Modifier</span>
                            <span class="font-bold text-red-500 dark:text-red-400" x-text="(burnMod >= 0 ? '+' : '') + burnMod + '%'">+0%</span>
                        </div>
                        <input type="range" min="-50" max="100" step="5" x-model.number="burnMod" @input="runSimulation()" class="w-full accent-red-500 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-neutral-400">
                            <span>-50% (Extreme Frugal)</span>
                            <span>Current</span>
                            <span>+100% (Lifestyle Creep)</span>
                        </div>
                    </div>

                    {{-- Slider 3: Immediate One-Off Shock --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-neutral-600 dark:text-slate-300 font-medium">One-Off Shock / Down Payment</span>
                            <span class="font-bold text-amber-500" x-text="'Rs ' + Number(oneOffShock).toLocaleString('en-IN')">Rs 0</span>
                        </div>
                        <input type="range" min="0" max="2500000" step="50000" x-model.number="oneOffShock" @input="runSimulation()" class="w-full accent-amber-500 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-neutral-400">
                            <span>Rs 0</span>
                            <span>Rs 10 Lakh</span>
                            <span>Rs 25 Lakh</span>
                        </div>
                    </div>

                    {{-- Slider 4: Investment Return Rate --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-neutral-600 dark:text-slate-300 font-medium">Expected Annual Market Return</span>
                            <span class="font-bold text-indigo-500" x-text="(marketReturn >= 0 ? '+' : '') + marketReturn + '%'">+12%</span>
                        </div>
                        <input type="range" min="-40" max="30" step="2" x-model.number="marketReturn" @input="runSimulation()" class="w-full accent-indigo-500 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-neutral-400">
                            <span>-40% (Severe Crash)</span>
                            <span>12% (Nifty Avg)</span>
                            <span>+30% (Super Bull)</span>
                        </div>
                    </div>

                    {{-- Slider 5: Horizon --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-neutral-600 dark:text-slate-300 font-medium">Simulation Horizon</span>
                            <span class="font-bold text-neutral-900 dark:text-white" x-text="simMonths + ' Months'">36 Months</span>
                        </div>
                        <input type="range" min="12" max="60" step="6" x-model.number="simMonths" @input="runSimulation()" class="w-full accent-neutral-800 dark:accent-white cursor-pointer">
                        <div class="flex justify-between text-[10px] text-neutral-400">
                            <span>1 Year</span>
                            <span>3 Years</span>
                            <span>5 Years</span>
                        </div>
                    </div>

                    {{-- Key Simulation Metrics Box --}}
                    <div class="p-4 rounded-2xl bg-neutral-100/80 dark:bg-white/5 border border-neutral-200 dark:border-white/10 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-slate-400">Simulated Monthly Net:</span>
                            <span class="font-bold" :class="simMonthlyNet >= 0 ? 'text-emerald-500' : 'text-red-500'" x-text="'Rs ' + Number(Math.round(simMonthlyNet)).toLocaleString('en-IN')">Rs 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-slate-400">Estimated Runway:</span>
                            <span class="font-bold text-cyan-500" x-text="runwayText">Infinite</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-slate-400">Median Net Worth @ End:</span>
                            <span class="font-bold text-neutral-900 dark:text-white" x-text="'Rs ' + Number(Math.round(endMedianNetWorth)).toLocaleString('en-IN')">Rs 0</span>
                        </div>
                    </div>

                    {{-- AI Battle Plan CTA --}}
                    <button @click="generateAiStrategy()" 
                            :disabled="isLoadingStrategy"
                            type="button"
                            class="w-full btn-primary text-xs py-3 flex items-center justify-center gap-2 shadow-cyan-500/20">
                        <span x-show="!isLoadingStrategy">⚡ Generate AI Crisis Defense Plan</span>
                        <span x-show="isLoadingStrategy" class="flex items-center gap-2">
                            <span class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                            <span>Strategizing...</span>
                        </span>
                    </button>
                </div>

                {{-- Right 2 cols: Monte Carlo Probability Chart + Strategy Feed --}}
                <div class="col-span-2 space-y-6">

                    {{-- Chart Card --}}
                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                            <div>
                                <h3 class="text-base font-bold text-neutral-900 dark:text-white">Monte Carlo Net Worth Projection (1,000 Runs)</h3>
                                <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">Shaded confidence corridor: 90th percentile (Bull), 50th (Median), 10th (Worst-Case)</p>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="flex items-center gap-1.5"><span class="w-3 h-1 bg-emerald-500 rounded"></span> Bull (90%)</span>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-1 bg-cyan-500 rounded"></span> Median (50%)</span>
                                <span class="flex items-center gap-1.5"><span class="w-3 h-1 bg-red-400 rounded"></span> Bear (10%)</span>
                            </div>
                        </div>

                        <div class="relative w-full" style="height: 320px;">
                            <canvas id="monteCarloChart"></canvas>
                        </div>
                    </div>

                    {{-- AI Tactical Defense Battle Plan Box --}}
                    <div class="glass-card p-6 border-l-4 border-l-cyan-500 relative overflow-hidden">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 flex items-center gap-2">
                                <span>🛡️</span> AI Crisis Defense Battle Plan
                            </h4>
                            <span class="text-[10px] text-neutral-400" x-text="strategyScenarioName">Reality Baseline</span>
                        </div>

                        <div class="space-y-2.5">
                            <template x-for="(bullet, index) in aiStrategyBullets" :key="index">
                                <div class="flex items-start gap-2.5 text-xs text-neutral-700 dark:text-slate-200 bg-neutral-100/50 dark:bg-white/[0.03] p-3 rounded-xl border border-neutral-200/50 dark:border-white/5">
                                    <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-500 flex items-center justify-center font-bold text-[10px] flex-shrink-0" x-text="index + 1"></span>
                                    <p class="leading-relaxed" x-text="bullet"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- Jarvis Voice CFO Embedded Component --}}
        <x-jarvis-voice />
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
    function timeMachineSimulator(config) {
        return {
            baseCash: config.initialCash,
            baseBurn: config.initialBurn,
            baseIncome: config.initialIncome,
            baseInvested: config.initialInvested,
            baseNetWorth: config.initialNetWorth,

            // Sliders
            incomeMod: 0,
            burnMod: 0,
            oneOffShock: 0,
            marketReturn: 12,
            simMonths: 36,

            activePreset: 'baseline',
            survivalRate: 100,
            runwayText: 'Infinite Runway',
            simMonthlyNet: 0,
            endMedianNetWorth: 0,

            isLoadingStrategy: false,
            strategyScenarioName: 'Current Trajectory',
            aiStrategyBullets: [
                "Your baseline cash flow is positive. Maintain your liquid emergency reserve in short-term debt funds.",
                "Direct monthly surplus systematically into diversified equity index funds to maximize compounding.",
                "Review tax deductions under Section 80C and 80D to avoid surplus leakage to income tax."
            ],

            chartInstance: null,

            applyPreset(type) {
                this.activePreset = type;
                if (type === 'startup') {
                    this.incomeMod = -100;
                    this.burnMod = -20;
                    this.oneOffShock = 50000;
                    this.marketReturn = 10;
                    this.strategyScenarioName = 'Startup Leap Scenario';
                } else if (type === 'crash') {
                    this.incomeMod = 0;
                    this.burnMod = 15;
                    this.oneOffShock = 0;
                    this.marketReturn = -35;
                    this.strategyScenarioName = 'Market Crash 2008 Scenario';
                } else if (type === 'homeloan') {
                    this.incomeMod = 0;
                    this.burnMod = 35; // EMI impact
                    this.oneOffShock = 1500000; // Down payment
                    this.marketReturn = 12;
                    this.strategyScenarioName = 'Mega Home Purchase';
                } else if (type === 'inflation') {
                    this.incomeMod = 0;
                    this.burnMod = 25;
                    this.oneOffShock = 100000;
                    this.marketReturn = 5;
                    this.strategyScenarioName = 'Hyperinflation Shock';
                }
                this.runSimulation();
            },

            resetToBaseline() {
                this.activePreset = 'baseline';
                this.incomeMod = 0;
                this.burnMod = 0;
                this.oneOffShock = 0;
                this.marketReturn = 12;
                this.strategyScenarioName = 'Current Trajectory';
                this.runSimulation();
            },

            runSimulation() {
                const months = this.simMonths;
                const effIncome = this.baseIncome * (1 + this.incomeMod / 100);
                const effBurn = this.baseBurn * (1 + this.burnMod / 100);
                this.simMonthlyNet = effIncome - effBurn;

                // Starting liquid cash & assets after immediate one-off shock
                let startingCash = Math.max(0, this.baseCash - this.oneOffShock);
                let startingInvested = this.baseInvested;
                if (this.oneOffShock > this.baseCash) {
                    startingInvested = Math.max(0, startingInvested - (this.oneOffShock - this.baseCash));
                }

                // Run 500 Monte Carlo iterations
                const iterations = 500;
                const allRuns = [];
                let survivedCount = 0;

                const monthlyReturnMean = (this.marketReturn / 100) / 12;
                const monthlyVol = 0.045; // ~15% annualized volatility

                for (let iter = 0; iter < iterations; iter++) {
                    const path = [];
                    let cash = startingCash;
                    let invested = startingInvested;
                    let isSolvent = true;

                    for (let m = 0; m <= months; m++) {
                        if (m === 0) {
                            path.push(cash + invested);
                            continue;
                        }

                        // Stochastic shock: Gaussian random return
                        const u1 = Math.random() || 0.0001;
                        const u2 = Math.random() || 0.0001;
                        const z = Math.sqrt(-2.0 * Math.log(u1)) * Math.cos(2.0 * Math.PI * u2);
                        const shockReturn = monthlyReturnMean + z * monthlyVol;

                        invested = Math.max(0, invested * (1 + shockReturn));

                        // Monthly cash flow addition/deduction
                        cash += this.simMonthlyNet;

                        // If cash goes negative, liquidate investments
                        if (cash < 0) {
                            if (invested >= Math.abs(cash)) {
                                invested += cash;
                                cash = 0;
                            } else {
                                cash += invested;
                                invested = 0;
                                isSolvent = false;
                            }
                        }

                        path.push(cash + invested);
                    }

                    if (isSolvent && (cash + invested) > 0) {
                        survivedCount++;
                    }
                    allRuns.push(path);
                }

                this.survivalRate = Math.round((survivedCount / iterations) * 100);

                // Compute percentiles for each month
                const labels = [];
                const p10 = [];
                const p50 = [];
                const p90 = [];

                for (let m = 0; m <= months; m++) {
                    labels.push(m === 0 ? 'Now' : 'M' + m);
                    const monthValues = allRuns.map(r => r[m]).sort((a, b) => a - b);
                    p10.push(Math.round(monthValues[Math.floor(iterations * 0.10)]));
                    p50.push(Math.round(monthValues[Math.floor(iterations * 0.50)]));
                    p90.push(Math.round(monthValues[Math.floor(iterations * 0.90)]));
                }

                this.endMedianNetWorth = p50.at(-1);

                // Runway calculation
                if (this.simMonthlyNet < 0) {
                    const totalLiquidity = startingCash + startingInvested;
                    const burnSpeed = Math.abs(this.simMonthlyNet);
                    const r = (totalLiquidity / burnSpeed).toFixed(1);
                    this.runwayText = `${r} Months Runway`;
                } else {
                    this.runwayText = 'Cash Flow Positive (Infinite)';
                }

                this.renderChart(labels, p10, p50, p90);
            },

            renderChart(labels, p10, p50, p90) {
                const canvas = document.getElementById('monteCarloChart');
                if (!canvas) return;

                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
                const tickColor = isDark ? '#94a3b8' : '#6b7280';

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }

                const ctx = canvas.getContext('2d');
                this.chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Bull Case (90th%)',
                                data: p90,
                                borderColor: '#10b981',
                                borderWidth: 1.5,
                                pointRadius: 0,
                                tension: 0.35,
                                fill: false,
                            },
                            {
                                label: 'Median Expectation (50th%)',
                                data: p50,
                                borderColor: '#06b6d4',
                                backgroundColor: isDark ? 'rgba(6,182,212,0.1)' : 'rgba(6,182,212,0.08)',
                                borderWidth: 3,
                                pointRadius: 0,
                                tension: 0.35,
                                fill: true,
                            },
                            {
                                label: 'Bear / Worst-Case (10th%)',
                                data: p10,
                                borderColor: '#f87171',
                                borderWidth: 1.5,
                                pointRadius: 0,
                                tension: 0.35,
                                fill: false,
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: c => ` ${c.dataset.label}: Rs ${Number(c.raw).toLocaleString('en-IN')}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: gridColor },
                                ticks: { color: tickColor, maxTicksLimit: 12, font: { size: 10 } }
                            },
                            y: {
                                grid: { color: gridColor },
                                ticks: {
                                    color: tickColor,
                                    font: { size: 10 },
                                    callback: v => 'Rs ' + Number(v).toLocaleString('en-IN', { maximumFractionDigits: 0 })
                                }
                            }
                        }
                    }
                });
            },

            async generateAiStrategy() {
                this.isLoadingStrategy = true;
                try {
                    const res = await fetch('{{ route('time-machine.strategy') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            scenario_name: this.strategyScenarioName,
                            survival_rate: this.survivalRate,
                            runway_months: this.simMonthlyNet < 0 ? parseFloat(this.runwayText) || 12 : 99,
                            projected_cash: this.endMedianNetWorth
                        })
                    });
                    const data = await res.json();
                    if (data.strategy && data.strategy.length > 0) {
                        this.aiStrategyBullets = data.strategy;
                    }
                } catch (e) {
                    console.error('Failed to generate AI strategy:', e);
                } finally {
                    this.isLoadingStrategy = false;
                }
            }
        };
    }
    </script>
</x-app-layout>
