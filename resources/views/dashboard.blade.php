<x-app-layout>
    <div x-data="{
        openModal: false,
        tab: 'expense',
        type: 'expense',
        amount: '',
        category: 'Food & Dining',
        date: '{{ now()->format('Y-m-d') }}',
        description: '',
        isScanning: false,
        scanError: '',
        scanSuccess: '',
        previewImage: null,

        openQuickAdd(initialTab = 'expense') {
            this.tab = initialTab;
            this.type = initialTab === 'income' ? 'income' : 'expense';
            this.scanError = '';
            this.scanSuccess = '';
            this.openModal = true;
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.uploadAndScan(file);
        },

        handleFileDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file) return;
            this.uploadAndScan(file);
        },

        async uploadAndScan(file) {
            this.isScanning = true;
            this.scanError = '';
            this.scanSuccess = '';

            const reader = new FileReader();
            reader.onload = (e) => { this.previewImage = e.target.result; };
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('receipt', file);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch('{{ route('receipt.scan') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.amount !== undefined && data.amount > 0) {
                    this.amount = data.amount;
                    this.category = data.category || 'Shopping';
                    this.date = data.date || '{{ now()->format('Y-m-d') }}';
                    this.description = data.description || (data.merchant ? 'At ' + data.merchant : '');
                    this.type = 'expense';
                    this.tab = 'expense';
                    this.scanSuccess = '✨ Receipt scanned! Pre-filled with Gemini Vision.';
                } else {
                    this.amount = data.amount || '';
                    this.category = data.category || 'Shopping';
                    this.date = data.date || '{{ now()->format('Y-m-d') }}';
                    this.description = data.description || '';
                    this.type = 'expense';
                    this.tab = 'expense';
                    this.scanSuccess = data.message || 'Details ready for manual verification.';
                }
            } catch (err) {
                this.scanError = 'Scan request failed. You can enter details manually.';
            } finally {
                this.isScanning = false;
            }
        },

        smsText: '',
        isParsingSms: false,

        async parseSmsText() {
            if (!this.smsText.trim()) return;
            this.isParsingSms = true;
            this.scanError = '';
            this.scanSuccess = '';

            try {
                const res = await fetch('{{ route('sms.parse') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ sms: this.smsText })
                });
                const data = await res.json();
                if (data.amount && data.amount > 0) {
                    this.amount = data.amount;
                    this.category = data.category || 'Food & Dining';
                    this.date = data.date || '{{ now()->format('Y-m-d') }}';
                    this.type = data.type || 'expense';
                    this.description = data.merchant ? 'Paid to ' + data.merchant : 'UPI Payment';
                    this.tab = this.type;
                    this.scanSuccess = `✨ Parsed ${data.merchant} (Rs ${data.amount}) from SMS!`;
                } else {
                    this.scanError = 'Could not detect amount from SMS. Please verify manually.';
                }
            } catch (err) {
                this.scanError = 'SMS parsing failed.';
            } finally {
                this.isParsingSms = false;
            }
        }
    }" 
    x-init="window.dashboardQuickAdd = (tab) => openQuickAdd(tab)"
    @open-quick-add.window="openQuickAdd($event.detail)"
    @keydown.escape.window="openModal = false">

        {{-- Header with action buttons --}}
        <x-slot name="header">
            <div x-data class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h2 class="font-semibold text-xl text-neutral-900 dark:text-white leading-tight">
                        {{ __('Real-Time Wealth & CFO Dashboard') }}
                    </h2>
                    <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">Live cash flow telemetry, AI vision receipt auditing, and budget burn rate.</p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @if($totalIncome == 0 && $totalExpenses == 0)
                    <form method="POST" action="{{ route('demo.seed') }}" onsubmit="return confirm('Load realistic demo portfolio with incomes, expenses, budgets & goals?');">
                        @csrf
                        <button type="submit" class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5 border-dashed border-cyan-400/50 text-cyan-600 dark:text-cyan-300">
                            <span>🚀</span> Load Demo Portfolio
                        </button>
                    </form>
                    @endif

                    <button @click="$dispatch('open-quick-add', 'scan')" 
                            onclick="window.dashboardQuickAdd ? window.dashboardQuickAdd('scan') : window.dispatchEvent(new CustomEvent('open-quick-add', { detail: 'scan' }))" 
                            type="button" 
                            class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Scan Receipt</span>
                    </button>

                    <button @click="$dispatch('open-quick-add', 'sms')" 
                            onclick="window.dashboardQuickAdd ? window.dashboardQuickAdd('sms') : window.dispatchEvent(new CustomEvent('open-quick-add', { detail: 'sms' }))" 
                            type="button" 
                            class="btn-secondary text-xs py-2 px-3 flex items-center gap-1.5 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 cursor-pointer">
                        <span>📱</span>
                        <span>Paste UPI SMS</span>
                    </button>

                    <button @click="$dispatch('open-quick-add', 'expense')" 
                            onclick="window.dashboardQuickAdd ? window.dashboardQuickAdd('expense') : window.dispatchEvent(new CustomEvent('open-quick-add', { detail: 'expense' }))" 
                            type="button" 
                            class="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 shadow-cyan-500/20 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Quick Add</span>
                    </button>
                </div>
            </div>
        </x-slot>

        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

                {{-- Flash status notification --}}
                @if(session('status'))
                <div class="bg-cyan-500/10 border border-cyan-500/20 text-cyan-700 dark:text-cyan-300 px-4 py-3 rounded-2xl flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white text-xs">&times;</button>
                </div>
                @endif

                {{-- Welcome + FinScore --}}
                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="col-span-2 glass-card p-6 relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute -right-10 -top-10 w-44 h-44 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div>
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <p class="section-label">Live Financial Pulse</p>
                                <span class="text-xs text-neutral-400 dark:text-slate-500">{{ now()->format('l, d F Y') }}</span>
                            </div>
                            <h3 class="mt-2 text-lg font-semibold text-neutral-900 dark:text-white">Welcome back, {{ auth()->user()->name }}!</h3>
                            <p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">
                                @if($taxSavingsNeeded > 0)
                                You have <strong class="text-neutral-700 dark:text-slate-200">Rs {{ number_format($taxSavingsNeeded) }}</strong> remaining to invest in 80C to maximize Old Regime tax benefits.
                                @else
                                You have fully maximized your Rs 1.5L 80C tax shelter for this financial cycle!
                                @endif
                            </p>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-3 text-sm">
                            <span class="badge-accent">Savings rate: {{ $savingsRate }}%</span>
                            <span class="badge-accent">Net cash flow: {{ format_money($netBalance) }}</span>
                            <span class="badge-accent">Expense ratio: {{ $expenseRatio }}%</span>
                        </div>
                    </div>
                    
                    {{-- FinScore Widget --}}
                    <div class="glass-card p-6 flex flex-col items-center justify-center text-center relative overflow-hidden">
                        <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
                        <h3 class="text-sm font-semibold text-neutral-900 dark:text-white mb-2">FinScore Health</h3>
                        <div class="relative w-24 h-24 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-neutral-200 dark:text-white/10" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                                <path class="{{ $financialScore > 750 ? 'text-emerald-500' : ($financialScore > 600 ? 'text-cyan-500' : 'text-red-500') }}" stroke-dasharray="{{ $financialScore / 10 }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                            </svg>
                            <div class="absolute text-xl font-bold {{ $financialScore > 750 ? 'text-emerald-500' : ($financialScore > 600 ? 'text-cyan-500' : 'text-red-500') }}">{{ $financialScore }}</div>
                        </div>
                        <p class="mt-2 text-xs text-neutral-500 dark:text-slate-400">
                            @if($financialScore >= 750)
                            <span class="text-emerald-500 font-medium">Excellent</span> &bull; Prime Credit Tier
                            @elseif($financialScore >= 600)
                            <span class="text-cyan-500 font-medium">Good</span> &bull; Room to Optimize
                            @else
                            <span class="text-red-400 font-medium">Needs Attention</span> &bull; High Outflows
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Zen & Quant Nexus Spotlight Launcher --}}
                <div class="glass-card p-5 border border-cyan-500/30 bg-gradient-to-r from-cyan-950/20 via-indigo-950/20 to-purple-950/20 relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-indigo-500/20 border border-cyan-500/30 flex items-center justify-center text-2xl flex-shrink-0 shadow-lg">
                            ⚡
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">Zen & Quant Nexus</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Behavioral • Quant • Growth</span>
                            </div>
                            <h4 class="text-sm font-bold text-neutral-900 dark:text-white mt-0.5">
                                Real-Time Financial Independence Ticker & Capital Council War Room
                            </h4>
                            <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">
                                Audit discretionary spending on the 4-D capital radar, evaluate behavioral utility, compounding opportunity cost & growth ROI before major purchases, and capture overnight idle cash yield (+4.5%).
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('nexus.index') }}"
                       class="btn-primary text-xs py-2 px-4 flex items-center gap-1.5 shadow-cyan-500/20 bg-gradient-to-r from-cyan-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 flex-shrink-0 relative z-10">
                        <span>Enter Nexus</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                {{-- Hidden Subscriptions Alert --}}
                @if($hiddenSubscriptions->count() > 0)
                <div class="bg-red-500/10 border border-red-500/20 rounded-2xl p-4 flex items-start gap-4">
                    <div class="bg-red-500/20 p-2 rounded-full flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-600 dark:text-red-400">Hidden Subscription Creep Detected!</h3>
                        <p class="text-sm text-red-500/80 dark:text-red-300 mt-1">We noticed recurring payments for <strong>{{ $hiddenSubscriptions->first()->category }} (Rs {{ number_format($hiddenSubscriptions->first()->amount) }})</strong>. Canceling unused subscriptions could boost your FinScore.</p>
                    </div>
                </div>
                @endif

                {{-- AI Financial Doctor Insights Bar --}}
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach($insights as $insight)
                    <div class="glass-card p-4 flex flex-col justify-between border-l-4 {{ $insight['type'] === 'danger' ? 'border-l-red-500' : ($insight['type'] === 'warning' ? 'border-l-amber-500' : ($insight['type'] === 'success' ? 'border-l-emerald-500' : 'border-l-cyan-500')) }}">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $insight['icon'] }}</span>
                                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:text-slate-300">{{ $insight['title'] }}</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $insight['type'] === 'danger' ? 'bg-red-500/10 text-red-500' : ($insight['type'] === 'warning' ? 'bg-amber-500/10 text-amber-500' : ($insight['type'] === 'success' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-cyan-500/10 text-cyan-400')) }}">
                                {{ $insight['badge'] }}
                            </span>
                        </div>
                        <p class="text-xs text-neutral-500 dark:text-slate-400 leading-relaxed">{{ $insight['text'] }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- KPI cards --}}
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <div class="glass-card p-5">
                        <div class="text-sm font-medium text-neutral-500 dark:text-slate-400">Total Income</div>
                        <div class="mt-4 text-2xl font-semibold text-emerald-600 dark:text-emerald-400 count-up" data-target="{{ $totalIncome }}">{{ format_money($totalIncome, 2) }}</div>
                    </div>
                    <div class="glass-card p-5">
                        <div class="text-sm font-medium text-neutral-500 dark:text-slate-400">Total Expenses</div>
                        <div class="mt-4 text-2xl font-semibold text-red-500 dark:text-red-400 count-up" data-target="{{ $totalExpenses }}">{{ format_money($totalExpenses, 2) }}</div>
                    </div>
                    <div class="glass-card p-5">
                        <div class="text-sm font-medium text-neutral-500 dark:text-slate-400">Budgets Active</div>
                        <div class="mt-4 text-2xl font-semibold text-neutral-900 dark:text-white">{{ $budgetCount }}</div>
                    </div>
                    <div class="glass-card p-5">
                        <div class="text-sm font-medium text-neutral-500 dark:text-slate-400">Financial Goals</div>
                        <div class="mt-4 text-2xl font-semibold text-neutral-900 dark:text-white">{{ $goalCount }}</div>
                    </div>
                    <div class="glass-card p-5">
                        <div class="text-sm font-medium text-neutral-500 dark:text-slate-400">Net Balance</div>
                        <div class="mt-4 text-2xl font-semibold text-cyan-600 dark:text-cyan-400">{{ format_money($netBalance, 2) }}</div>
                    </div>
                </div>

                {{-- Middle Row: Predictive Cash Flow + Expense Category Donut --}}
                <div class="grid gap-6 lg:grid-cols-3">
                    {{-- Left 2 cols: Cash flow --}}
                    <div class="col-span-2 glass-card p-6 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-4">
                            <div>
                                <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Predictive Cash Flow</h3>
                                <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">Last 6 months actuals + 3-month AI regression forecast</p>
                            </div>
                            
                            {{-- Scenario Simulator --}}
                            <div class="flex items-center gap-2 bg-neutral-100 dark:bg-white/5 rounded-lg p-1.5 border border-neutral-200 dark:border-white/10">
                                <span class="text-xs text-neutral-500 px-2 font-medium">What-If EMI:</span>
                                <input type="number" id="scenario-emi" placeholder="Rs EMI" class="glass-input text-xs w-24 py-1 px-2 h-7" oninput="updateScenario()">
                            </div>
                        </div>
                        <div class="relative w-full" style="height: 270px;">
                            <canvas id="cashFlowChart"></canvas>
                        </div>
                    </div>

                    {{-- Right 1 col: Category Spending Donut --}}
                    <div class="glass-card p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Spend Breakdown</h3>
                                <span class="text-xs text-neutral-400">{{ format_money($categorySpendData['grandTotal']) }}</span>
                            </div>
                            <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">Distribution across expense categories</p>
                        </div>

                        @if($categorySpendData['grandTotal'] > 0)
                        <div class="relative my-2 flex items-center justify-center" style="height: 180px;">
                            <canvas id="categoryDonutChart"></canvas>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                                <span class="text-[11px] uppercase tracking-wider text-neutral-400">Total Spend</span>
                                <span class="text-sm font-bold text-neutral-900 dark:text-white">{{ format_money($categorySpendData['grandTotal'], 0) }}</span>
                            </div>
                        </div>

                        {{-- Legend items --}}
                        <div class="mt-2 space-y-1.5 max-h-32 overflow-y-auto pr-1">
                            @foreach($categorySpendData['labels'] as $idx => $label)
                            <div class="flex items-center justify-between text-xs py-0.5 border-b border-neutral-100 dark:border-white/5 last:border-0">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $categorySpendData['colors'][$idx] }}"></span>
                                    <span class="truncate text-neutral-700 dark:text-slate-300">{{ $label }}</span>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="text-neutral-500 dark:text-slate-400">{{ format_money($categorySpendData['totals'][$idx], 0) }}</span>
                                    <span class="font-semibold text-neutral-900 dark:text-white">{{ $categorySpendData['percentages'][$idx] }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="py-12 text-center text-xs text-neutral-400 dark:text-slate-500">
                            <p>No expense data recorded yet.</p>
                            <button @click="openQuickAdd('expense')" class="mt-2 text-cyan-600 dark:text-cyan-400 hover:underline">Log your first expense &rarr;</button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Active Budgets Burn-Rate Section --}}
                <div class="glass-card p-6">
                    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                        <div>
                            <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Active Monthly Budgets (Burn Rate & Velocity)</h3>
                            <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">Real-time expenditure tracking against monthly allocations</p>
                        </div>
                        <a href="{{ route('budgets.index') }}" class="text-xs text-cyan-600 dark:text-cyan-400 hover:text-cyan-500 font-medium flex items-center gap-1">
                            <span>Manage All Budgets</span> &rarr;
                        </a>
                    </div>

                    @if($activeBudgets->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($activeBudgets as $budget)
                        <div class="p-4 rounded-xl border {{ $budget['status'] === 'danger' ? 'border-red-500/30 bg-red-500/5' : ($budget['status'] === 'warning' ? 'border-amber-500/30 bg-amber-500/5' : 'border-neutral-200 dark:border-white/10 bg-neutral-50/50 dark:bg-white/[0.02]') }}">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-semibold text-sm text-neutral-900 dark:text-white truncate">{{ $budget['category'] }}</span>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full {{ $budget['status'] === 'danger' ? 'bg-red-500 text-white' : ($budget['status'] === 'warning' ? 'bg-amber-500 text-slate-900' : 'bg-emerald-500/20 text-emerald-600 dark:text-emerald-400') }}">
                                    @if($budget['status'] === 'danger') Over Budget @elseif($budget['status'] === 'warning') 80%+ Spent @else On Track @endif
                                </span>
                            </div>

                            <div class="flex items-baseline justify-between text-xs text-neutral-500 dark:text-slate-400 mb-1.5">
                                <span>Spent: <strong class="text-neutral-800 dark:text-white">{{ format_money($budget['spent'], 0) }}</strong></span>
                                <span>Limit: {{ format_money($budget['limit'], 0) }}</span>
                            </div>

                            {{-- Progress bar --}}
                            <div class="w-full bg-neutral-200 dark:bg-white/10 rounded-full h-2 overflow-hidden mb-2">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $budget['status'] === 'danger' ? 'bg-red-500' : ($budget['status'] === 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                     style="width: {{ min(100, $budget['percent']) }}%"></div>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-neutral-400 dark:text-slate-400">
                                <span>{{ $budget['percent'] }}% used</span>
                                @if($budget['status'] === 'danger')
                                <span class="text-red-500 font-medium">Exceeded by {{ format_money($budget['spent'] - $budget['limit'], 0) }}</span>
                                @elseif($budget['days_left'] > 0)
                                <span>{{ format_money($budget['daily_allowance'], 0) }}/day ({{ $budget['days_left'] }}d left)</span>
                                @else
                                <span>Cycle completed</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="py-8 text-center text-xs text-neutral-400 dark:text-slate-500">
                        <p>No active budgets created for this month.</p>
                        <a href="{{ route('budgets.create') }}" class="mt-2 inline-block text-cyan-600 dark:text-cyan-400 font-medium hover:underline">+ Create your first budget &rarr;</a>
                    </div>
                    @endif
                </div>

                {{-- Financial Multiverse Flagship Spotlight Banner --}}
                <div class="glass-card p-6 border-purple-500/40 relative overflow-hidden bg-gradient-to-r from-purple-950/30 via-slate-900/50 to-slate-950/30 shadow-xl">
                    <div class="absolute -right-12 -bottom-12 w-56 h-56 bg-purple-500/15 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="flex items-center justify-between flex-wrap gap-4 relative z-10">
                        <div class="max-w-2xl">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🌌</span>
                                <span class="section-label text-purple-600 dark:text-purple-400">Evolutionary Breakthrough</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                    Git for Your Money
                                </span>
                            </div>
                            <h3 class="mt-2 text-xl font-extrabold text-neutral-900 dark:text-white">
                                The Financial Multiverse & AI Digital Twin
                            </h3>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-slate-400 leading-relaxed">
                                Branch parallel economic futures (<code class="text-purple-400">git checkout -b fire-at-42</code>), inspect live financial diffs, chat with your Future Self in Year 2038, and merge winning lifestyles into active reality with 1 click (<code class="text-emerald-400">git merge</code>).
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('multiverse.index') }}" class="btn-primary text-xs py-3 px-5 flex items-center gap-2 shadow-purple-500/25 bg-purple-600 hover:bg-purple-500 font-bold">
                                <span>Enter Multiverse Studio</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Financial Time Machine & Impulse Shield Dual Banners --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Financial Time Machine Banner --}}
                    <div class="glass-card p-6 border-cyan-500/30 relative overflow-hidden bg-gradient-to-br from-cyan-950/20 via-slate-900/40 to-slate-950/20 flex flex-col justify-between">
                        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">⏳</span>
                                <span class="section-label">Stochastic Simulator</span>
                            </div>
                            <h3 class="mt-2 text-lg font-bold text-neutral-900 dark:text-white">Financial Time Machine</h3>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-slate-400">
                                Run 1,000 stochastic economic simulations to stress-test your life decisions — quit your job for a startup, buy a house, or simulate a 2008 crash.
                            </p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200/50 dark:border-slate-800 flex items-center justify-between relative z-10">
                            <span class="text-[11px] text-cyan-400 font-mono">1,000 Monte Carlo Paths</span>
                            <a href="{{ route('time-machine.index') }}" class="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 shadow-cyan-500/20">
                                <span>Launch</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>

                    {{-- Impulse Shield Vault Banner --}}
                    <div class="glass-card p-6 border-rose-500/30 relative overflow-hidden bg-gradient-to-br from-rose-950/20 via-slate-900/40 to-slate-950/20 flex flex-col justify-between">
                        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🛡️</span>
                                    <span class="section-label text-rose-500 dark:text-rose-400">Anti-Impulse Vault</span>
                                </div>
                                @if(($impulseStats['active_count'] ?? 0) > 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 animate-pulse">
                                        {{ $impulseStats['active_count'] }} in Cooldown
                                    </span>
                                @endif
                            </div>
                            <h3 class="mt-2 text-lg font-bold text-neutral-900 dark:text-white">The Impulse Shield (48h Vault)</h3>
                            <p class="mt-1 text-xs text-neutral-500 dark:text-slate-400">
                                Convert impulsive purchases into working hours of life & 10-year compounding regret (12% CAGR). Lock temptations in a 48h vault.
                            </p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-200/50 dark:border-slate-800 flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-1.5 text-xs text-emerald-400 font-medium">
                                <span>🏆</span>
                                <span>Saved {{ format_money($impulseStats['resisted_savings'] ?? 0) }}</span>
                            </div>
                            <a href="{{ route('impulse-shield.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-lg bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-600/20 transition-all">
                                <span>Enter Vault</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Feature nav cards --}}
                <div class="grid gap-6 lg:grid-cols-4">
                    <a href="{{ route('expenses.index') }}" class="glass-card-hover p-6">
                        <p class="section-label">Feature 01</p>
                        <h3 class="mt-2 text-lg font-semibold text-neutral-900 dark:text-white">Cash Flow Tracking</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Multi-source income, smart categorization, and net balance view.</p>
                    </a>
                    <a href="{{ route('budgets.index') }}" class="glass-card-hover p-6">
                        <p class="section-label">Feature 02</p>
                        <h3 class="mt-2 text-lg font-semibold text-neutral-900 dark:text-white">Smart Budget Planner</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Stay within limits without feeling restricted.</p>
                    </a>
                    <a href="{{ route('goals.index') }}" class="glass-card-hover p-6">
                        <p class="section-label">Feature 03</p>
                        <h3 class="mt-2 text-lg font-semibold text-neutral-900 dark:text-white">Goal Tracking</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Set milestones, watch progress, stay motivated.</p>
                    </a>
                    <a href="{{ route('chat.index') }}" class="glass-card-hover p-6 border-cyan-400/20">
                        <p class="section-label">AI-Powered</p>
                        <h3 class="mt-2 text-lg font-semibold text-cyan-600 dark:text-cyan-300">AI Financial Advisor</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Personalized guidance + take actions via chat.</p>
                    </a>
                </div>
            </div>
        </div>

        {{-- Alpine.js Quick-Add & Receipt Scanner Modal --}}
        <div x-show="openModal" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="openModal = false"></div>

            {{-- Modal Content --}}
            <div class="relative bg-white dark:bg-slate-900 border border-neutral-200 dark:border-white/10 rounded-3xl shadow-2xl w-full max-w-lg p-6 overflow-hidden z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="scale-95 translate-y-4"
                 x-transition:enter-end="scale-100 translate-y-0">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between pb-4 border-b border-neutral-200 dark:border-white/10">
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Quick Add Transaction</h3>
                        <p class="text-xs text-neutral-500 dark:text-slate-400">Record an expense, income, or scan with AI Vision</p>
                    </div>
                    <button @click="openModal = false" type="button" class="text-neutral-400 hover:text-neutral-600 dark:hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Tabs --}}
                <div class="flex gap-2 my-4 bg-neutral-100 dark:bg-white/5 p-1 rounded-xl">
                    <button type="button" 
                            @click="tab = 'expense'; type = 'expense'"
                            :class="tab === 'expense' ? 'bg-white dark:bg-slate-800 text-neutral-900 dark:text-white shadow-sm font-semibold' : 'text-neutral-500 dark:text-slate-400 hover:text-neutral-900'"
                            class="flex-1 py-1.5 text-xs rounded-lg transition-all text-center">
                        - Expense
                    </button>
                    <button type="button" 
                            @click="tab = 'income'; type = 'income'"
                            :class="tab === 'income' ? 'bg-white dark:bg-slate-800 text-neutral-900 dark:text-white shadow-sm font-semibold' : 'text-neutral-500 dark:text-slate-400 hover:text-neutral-900'"
                            class="flex-1 py-1.5 text-xs rounded-lg transition-all text-center">
                        + Income
                    </button>
                    <button type="button" 
                            @click="tab = 'scan'"
                            :class="tab === 'scan' ? 'bg-cyan-500 text-white shadow-sm font-semibold' : 'text-cyan-600 dark:text-cyan-400 hover:text-cyan-500'"
                            class="flex-1 py-1.5 text-xs rounded-lg transition-all text-center flex items-center justify-center gap-1">
                        <span>📷</span> Bill OCR
                    </button>
                    <button type="button" 
                            @click="tab = 'sms'"
                            :class="tab === 'sms' ? 'bg-emerald-500 text-white shadow-sm font-semibold' : 'text-emerald-600 dark:text-emerald-400 hover:text-emerald-500'"
                            class="flex-1 py-1.5 text-xs rounded-lg transition-all text-center flex items-center justify-center gap-1">
                        <span>📱</span> Paste SMS
                    </button>
                </div>

                {{-- Notification / feedback inside modal --}}
                <div x-show="scanSuccess" x-cloak class="mb-3 text-xs p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400" x-text="scanSuccess"></div>
                <div x-show="scanError" x-cloak class="mb-3 text-xs p-2.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400" x-text="scanError"></div>

                {{-- Scan Receipt Dropzone Tab --}}
                <div x-show="tab === 'scan'" class="space-y-4">
                    <div @dragover.prevent
                         @drop.prevent="handleFileDrop($event)"
                         class="border-2 border-dashed border-neutral-300 dark:border-white/20 rounded-2xl p-6 text-center cursor-pointer hover:border-cyan-500 transition-colors bg-neutral-50/50 dark:bg-white/[0.02]"
                         @click="$refs.receiptInput.click()">
                        <input type="file" x-ref="receiptInput" @change="handleFileSelect($event)" accept="image/*,.pdf" class="hidden">
                        
                        <div x-show="!isScanning">
                            <svg class="mx-auto h-10 w-10 text-cyan-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-2 text-sm font-medium text-neutral-800 dark:text-slate-200">Drag & drop receipt or click to upload</p>
                            <p class="mt-1 text-xs text-neutral-400">Supports JPG, PNG, WEBP, PDF up to 10MB</p>
                        </div>

                        {{-- Scanning Spinner --}}
                        <div x-show="isScanning" class="py-4">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-cyan-500 border-t-transparent"></div>
                            <p class="mt-3 text-sm font-medium text-cyan-500">Gemini AI Vision analyzing receipt...</p>
                            <p class="text-xs text-neutral-400 mt-0.5">Extracting vendor, amount, date & category</p>
                        </div>
                    </div>

                    <p class="text-xs text-neutral-400 text-center">Receipt details will auto-populate the expense form instantly.</p>
                </div>

                {{-- Paste SMS Tab --}}
                <div x-show="tab === 'sms'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Paste Bank / UPI SMS:</label>
                        <textarea x-model="smsText" @input.debounce.500ms="parseSmsText()" rows="3" placeholder="e.g. Rs 450.00 debited from a/c **1234 on 20-Oct-26 to VPA swiggy@icici. Avl bal Rs 24,500.00" class="glass-input w-full text-xs"></textarea>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-neutral-400">Auto-detects HDFC, SBI, ICICI, Axis, Paytm, GPay & PhonePe</span>
                        <button type="button" @click="parseSmsText()" class="btn-primary text-xs py-1.5 px-3">
                            <span x-show="!isParsingSms">⚡ Auto-Parse SMS</span>
                            <span x-show="isParsingSms">Parsing...</span>
                        </button>
                    </div>

                    {{-- Sample SMS Quick Chips --}}
                    <div class="pt-2 border-t border-neutral-200 dark:border-white/10">
                        <span class="text-[10px] text-neutral-400 block mb-1">Click to test with real SMS sample:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="smsText = 'HDFC Bank: Rs 450.00 debited from a/c **1234 on 20-OCT-26 to VPA swiggy@icici (UPI Ref 429182). Avl bal Rs 24,500.00'; parseSmsText()" class="text-[10px] px-2 py-0.5 rounded bg-neutral-100 dark:bg-white/5 border border-neutral-200 dark:border-white/10 text-neutral-600 dark:text-slate-300 hover:border-cyan-400">
                                🍕 Swiggy Rs 450
                            </button>
                            <button type="button" @click="smsText = 'Dear SBI User, your A/C ending 4321 debited by Rs 180.00 on 20Oct26 by transfer to UPI/BLINKIT/429182. Bal: Rs 12,400.00'; parseSmsText()" class="text-[10px] px-2 py-0.5 rounded bg-neutral-100 dark:bg-white/5 border border-neutral-200 dark:border-white/10 text-neutral-600 dark:text-slate-300 hover:border-cyan-400">
                                🥦 Blinkit Rs 180
                            </button>
                            <button type="button" @click="smsText = 'Paid Rs. 40 to Chai Point via PhonePe on 20 Oct. UPI Ref: 987654321'; parseSmsText()" class="text-[10px] px-2 py-0.5 rounded bg-neutral-100 dark:bg-white/5 border border-neutral-200 dark:border-white/10 text-neutral-600 dark:text-slate-300 hover:border-cyan-400">
                                ☕ Chai Point Rs 40
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Manual Form (Expense or Income) --}}
                <form x-show="tab === 'expense' || tab === 'income'" method="POST" action="{{ route('quick-transaction.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" :value="type">

                    <div>
                        <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Amount (Rs) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-neutral-400 text-sm">Rs</span>
                            <input type="number" step="0.01" name="amount" x-model="amount" required placeholder="0.00" class="glass-input w-full pl-9">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div x-show="type === 'expense'">
                            <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Category *</label>
                            <select name="category" x-model="category" class="glass-input w-full text-sm">
                                @foreach($allCategories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="type === 'income'">
                            <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Source *</label>
                            <input type="text" name="source" placeholder="Salary, Freelance, Dividend" class="glass-input w-full text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Date *</label>
                            <input type="date" name="date" x-model="date" required class="glass-input w-full text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Description / Notes (Optional)</label>
                        <input type="text" name="description" x-model="description" placeholder="e.g. Swiggy lunch, petrol, monthly rent" class="glass-input w-full text-sm">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-neutral-200 dark:border-white/10">
                        <button type="button" @click="openModal = false" class="btn-secondary text-xs py-2 px-4">Cancel</button>
                        <button type="submit" class="btn-primary text-xs py-2 px-5">Save Transaction</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const cashFlowData = @json($cashFlowData);
        const categorySpendData = @json($categorySpendData);
        const isDark = document.documentElement.classList.contains('dark');

        const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
        const tickColor  = isDark ? '#94a3b8' : '#6b7280';
        const tooltipBg  = isDark ? '#1e293b' : '#ffffff';
        const tooltipTxt = isDark ? '#e2e8f0' : '#1f2937';

        // ── 1. Cash Flow Line Chart ──
        const allLabels     = [...cashFlowData.labels, ...cashFlowData.forecastLabels];
        const allIncome     = [...cashFlowData.incomeData,  ...cashFlowData.forecastIncome.map(() => null)];
        const allExpense    = [...cashFlowData.expenseData, ...cashFlowData.forecastExpense.map(() => null)];
        const forecastIncome  = [...cashFlowData.incomeData.map(() => null), cashFlowData.incomeData.at(-1), ...cashFlowData.forecastIncome];
        const forecastExpense = [...cashFlowData.expenseData.map(() => null), cashFlowData.expenseData.at(-1), ...cashFlowData.forecastExpense];

        const ctx = document.getElementById('cashFlowChart').getContext('2d');
        const cashFlowChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: allLabels,
                datasets: [
                    {
                        label: 'Income',
                        data: allIncome,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.08)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981',
                        tension: 0.4,
                        fill: true,
                        spanGaps: false,
                    },
                    {
                        label: 'Expenses',
                        data: allExpense,
                        borderColor: '#f87171',
                        backgroundColor: 'rgba(248,113,113,0.06)',
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#f87171',
                        tension: 0.4,
                        fill: true,
                        spanGaps: false,
                    },
                    {
                        label: 'Forecast Income',
                        data: forecastIncome,
                        borderColor: '#10b981',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointRadius: 3,
                        pointStyle: 'triangle',
                        pointBackgroundColor: '#10b981',
                        tension: 0.4,
                        fill: false,
                        spanGaps: false,
                    },
                    {
                        label: 'Forecast Expenses',
                        data: forecastExpense,
                        borderColor: '#f87171',
                        borderWidth: 2,
                        borderDash: [6, 4],
                        pointRadius: 3,
                        pointStyle: 'triangle',
                        pointBackgroundColor: '#f87171',
                        tension: 0.4,
                        fill: false,
                        spanGaps: false,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        labels: {
                            color: tickColor,
                            font: { size: 11 },
                            boxWidth: 12,
                            padding: 14,
                        }
                    },
                    tooltip: {
                        backgroundColor: tooltipBg,
                        titleColor: tooltipTxt,
                        bodyColor: tooltipTxt,
                        borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: ctx => {
                                if (ctx.raw === null) return null;
                                return ` ${ctx.dataset.label}: Rs ${Number(ctx.raw).toLocaleString('en-IN', {minimumFractionDigits:0})}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: tickColor, font: { size: 11 } }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            color: tickColor,
                            font: { size: 11 },
                            callback: v => 'Rs ' + Number(v).toLocaleString('en-IN', {maximumFractionDigits:0})
                        }
                    }
                }
            }
        });

        // ── 2. Expense Category Donut Chart ──
        if (categorySpendData.labels && categorySpendData.labels.length > 0 && categorySpendData.grandTotal > 0) {
            const donutElem = document.getElementById('categoryDonutChart');
            if (donutElem) {
                const donutCtx = donutElem.getContext('2d');
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categorySpendData.labels,
                        datasets: [{
                            data: categorySpendData.totals,
                            backgroundColor: categorySpendData.colors,
                            borderWidth: isDark ? 2 : 1,
                            borderColor: isDark ? '#0f172a' : '#ffffff',
                            hoverOffset: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: tooltipBg,
                                titleColor: tooltipTxt,
                                bodyColor: tooltipTxt,
                                borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                                borderWidth: 1,
                                padding: 10,
                                callbacks: {
                                    label: ctx => {
                                        const val = ctx.raw || 0;
                                        const total = categorySpendData.grandTotal || 1;
                                        const pct = Math.round((val / total) * 100);
                                        return ` ${ctx.label}: Rs ${Number(val).toLocaleString('en-IN')} (${pct}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '72%',
                    }
                });
            }
        }

        // Time Machine Scenario Simulator Logic
        function updateScenario() {
            const emiValue = parseFloat(document.getElementById('scenario-emi').value) || 0;
            const newForecastExpense = cashFlowData.forecastExpense.map(exp => exp + emiValue);
            const combinedExpense = [...cashFlowData.expenseData.map(() => null), cashFlowData.expenseData.at(-1), ...newForecastExpense];
            
            cashFlowChart.data.datasets[3].data = combinedExpense;
            cashFlowChart.update();
        }
    </script>

    {{-- Jarvis Voice CFO Embedded Voice Assistant --}}
    <x-jarvis-voice />
</x-app-layout>
