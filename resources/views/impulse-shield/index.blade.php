<x-app-layout>
    <div x-data="impulseCalculator({{ $monthlyIncome }})" class="py-6">

        <x-slot name="header">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🛡️</span>
                        <h2 class="font-bold text-xl text-neutral-900 dark:text-white leading-tight">
                            The Impulse Shield: Anti-Compulsion Vault
                        </h2>
                    </div>
                    <p class="text-xs text-neutral-500 dark:text-slate-400 mt-1">
                        Break impulse buying traps. Convert desires into hours of your life worked & 10-year compounding regret.
                    </p>
                </div>

                {{-- Total Resisted Saved Trophy --}}
                <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-2xl">
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-600 dark:text-emerald-400 block">Total Cash Saved</span>
                        <span class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">Rs {{ number_format($totalResistedMoney) }}</span>
                    </div>
                    <span class="text-2xl">🏆</span>
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

            {{-- Metric Strip --}}
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="glass-card p-4">
                    <span class="text-[11px] font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider block">Money Saved by Resisting</span>
                    <span class="text-2xl font-extrabold text-emerald-500 mt-1 block">Rs {{ number_format($totalResistedMoney) }}</span>
                    <span class="text-xs text-neutral-400 mt-1 block">{{ $resistedItems->count() }} impulses defeated</span>
                </div>

                <div class="glass-card p-4">
                    <span class="text-[11px] font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider block">Life-Energy Protected</span>
                    <span class="text-2xl font-extrabold text-cyan-500 mt-1 block">{{ $totalHoursSaved }} Hours</span>
                    <span class="text-xs text-neutral-400 mt-1 block">~{{ round($totalHoursSaved / 8, 1) }} working days of labor spared</span>
                </div>

                <div class="glass-card p-4">
                    <span class="text-[11px] font-medium text-neutral-500 dark:text-slate-400 uppercase tracking-wider block">Currently in Cooldown</span>
                    <span class="text-2xl font-extrabold text-amber-500 mt-1 block">{{ $coolingItems->count() }} Items</span>
                    <span class="text-xs text-neutral-400 mt-1 block">Rs {{ number_format($totalCoolingMoney) }} in 48-hour vault</span>
                </div>
            </div>

            {{-- Main Layout: Add Impulse Lock + Live Active Vault --}}
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Left 1 col: Live Impulse Evaluator Form --}}
                <div class="glass-card p-6 space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-neutral-900 dark:text-white">Tempted by a Purchase?</h3>
                        <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">Test its true cost before spending your hard-earned money.</p>
                    </div>

                    <form method="POST" action="{{ route('impulse-shield.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">What do you want to buy? *</label>
                            <input type="text" name="item_name" x-model="itemName" required placeholder="e.g. Nike Air Jordans, Sony WH-1000XM5, iPad" class="glass-input w-full text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Price (Rs) *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-neutral-400 text-sm">Rs</span>
                                <input type="number" step="1" name="price" x-model.number="price" required placeholder="4500" class="glass-input w-full pl-9 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Category</label>
                            <select name="category" class="glass-input w-full text-sm">
                                <option value="Shopping">Shopping & Fashion</option>
                                <option value="Electronics">Electronics & Gadgets</option>
                                <option value="Entertainment">Entertainment & Gaming</option>
                                <option value="Food & Dining">Luxury Dining / Party</option>
                                <option value="Other">Other Temptation</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-neutral-600 dark:text-slate-300 mb-1">Notes / Why do you want this?</label>
                            <input type="text" name="notes" placeholder="Seen on Instagram ad, saw friend wearing it..." class="glass-input w-full text-sm">
                        </div>

                        {{-- Real-Time True Cost Preview Box --}}
                        <div x-show="price > 0" class="p-4 rounded-2xl bg-neutral-100 dark:bg-white/5 border border-neutral-200 dark:border-white/10 space-y-2 text-xs">
                            <div class="text-[10px] uppercase font-bold tracking-wider text-neutral-400">The Brutal Reality Check:</div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-neutral-500 dark:text-slate-400 flex items-center gap-1">
                                    <span>⏳</span> Life-Energy Cost:
                                </span>
                                <span class="font-bold text-amber-500" x-text="calcWorkHours() + ' hrs of work'">0 hrs</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-neutral-500 dark:text-slate-400 flex items-center gap-1">
                                    <span>📈</span> 10-Yr Nifty Cost:
                                </span>
                                <span class="font-bold text-emerald-500" x-text="'Rs ' + Number(calcFutureValue()).toLocaleString('en-IN')">Rs 0</span>
                            </div>

                            <p class="text-[11px] text-neutral-500 dark:text-slate-400 pt-1 border-t border-neutral-200 dark:border-white/10 italic leading-snug">
                                Buying this trades <span class="font-semibold text-neutral-800 dark:text-white" x-text="calcWorkDays()"></span> full working days of your life.
                            </p>
                        </div>

                        <button type="submit" class="w-full btn-primary text-xs py-3 flex items-center justify-center gap-2 shadow-emerald-500/20">
                            <span>🛡️ Lock into 48-Hour Cooldown Vault</span>
                        </button>
                    </form>
                </div>

                {{-- Right 2 cols: Cooldown Vault & Hall of Fame --}}
                <div class="col-span-2 space-y-6">

                    {{-- Section 1: Active Cooling Items --}}
                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-neutral-900 dark:text-white">Active 48-Hour Vault</h3>
                                <p class="text-xs text-neutral-500 dark:text-slate-400 mt-0.5">Items currently under mandatory cooling period.</p>
                            </div>
                            <span class="text-xs bg-amber-500/10 text-amber-500 border border-amber-500/20 px-2.5 py-1 rounded-full font-semibold">
                                {{ $coolingItems->count() }} Locked
                            </span>
                        </div>

                        @if($coolingItems->isNotEmpty())
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach($coolingItems as $item)
                            <div class="p-4 rounded-2xl border border-neutral-200 dark:border-white/10 bg-neutral-50/50 dark:bg-white/[0.02] flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h4 class="font-bold text-sm text-neutral-900 dark:text-white">{{ $item->item_name }}</h4>
                                            <span class="text-[10px] text-neutral-400">{{ $item->category }} &bull; {{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                        <span class="text-sm font-extrabold text-neutral-900 dark:text-white">Rs {{ number_format($item->price) }}</span>
                                    </div>

                                    @if($item->notes)
                                    <p class="text-xs text-neutral-500 dark:text-slate-400 mt-2 italic">"{{ $item->notes }}"</p>
                                    @endif

                                    {{-- True cost tags --}}
                                    <div class="flex flex-wrap gap-2 mt-3 text-[11px]">
                                        <span class="bg-amber-500/10 text-amber-500 px-2 py-0.5 rounded-md font-medium">
                                            ⏳ {{ $item->lifeEnergyHours($monthlyIncome) }}h work
                                        </span>
                                        <span class="bg-emerald-500/10 text-emerald-500 px-2 py-0.5 rounded-md font-medium">
                                            📈 Rs {{ number_format($item->opportunityCost10y()) }} in 10y
                                        </span>
                                    </div>

                                    {{-- Timer --}}
                                    <div class="mt-4 p-2.5 rounded-xl bg-neutral-100 dark:bg-white/5 border border-neutral-200 dark:border-white/10 flex items-center justify-between text-xs">
                                        <span class="text-neutral-500 dark:text-slate-400 flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                                            <span>Vault Timer:</span>
                                        </span>
                                        @if(!$item->isExpired())
                                        <span class="font-bold text-cyan-500">{{ $item->hoursRemaining() }}h {{ $item->minutesRemaining() }}m left</span>
                                        @else
                                        <span class="font-bold text-emerald-500">Cooldown Complete!</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-4 pt-3 border-t border-neutral-200 dark:border-white/10 flex items-center gap-2">
                                    <form method="POST" action="{{ route('impulse-shield.resist', $item->id) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full text-xs py-2 px-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold transition-colors flex items-center justify-center gap-1 shadow-sm">
                                            <span>🏆</span> I Resisted!
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('impulse-shield.buy', $item->id) }}" onsubmit="return confirm('Are you sure you still want to buy this and log it as an expense?');">
                                        @csrf
                                        <button type="submit" class="text-xs py-2 px-3 rounded-xl border border-neutral-300 dark:border-white/20 text-neutral-600 dark:text-slate-300 hover:bg-neutral-100 dark:hover:bg-white/10 transition-colors">
                                            🛒 Buy
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="py-10 text-center text-xs text-neutral-400 dark:text-slate-500">
                            <span class="text-3xl block mb-2">🎉</span>
                            <p class="font-medium">No impulsive items currently trapped in the vault!</p>
                            <p class="mt-1">Whenever you feel an urge to buy an expensive gadget or sneakers, test it here first.</p>
                        </div>
                        @endif
                    </div>

                    {{-- Section 2: Victory Hall of Fame (Resisted Items) --}}
                    <div class="glass-card p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-base font-bold text-neutral-900 dark:text-white flex items-center gap-2">
                                <span>🏆</span> Victory Hall of Fame (Saved Purchases)
                            </h3>
                            <span class="text-xs text-emerald-500 font-bold">Rs {{ number_format($totalResistedMoney) }} Protected</span>
                        </div>

                        @if($resistedItems->isNotEmpty())
                        <div class="divide-y divide-neutral-100 dark:divide-white/5">
                            @foreach($resistedItems as $item)
                            <div class="py-2.5 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center text-xs font-bold">✓</span>
                                    <div>
                                        <span class="font-semibold text-neutral-800 dark:text-white">{{ $item->item_name }}</span>
                                        <span class="text-[10px] text-neutral-400 block">{{ $item->category }} &bull; Saved on {{ $item->updated_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-emerald-500">+Rs {{ number_format($item->price) }}</span>
                                    <span class="text-[10px] text-neutral-400 block">Saved ~{{ $item->lifeEnergyHours($monthlyIncome) }}h labor</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4 text-xs text-neutral-400 dark:text-slate-500">
                            No temptations defeated yet. Lock an impulsive desire above and resist it to earn your spot in the Hall of Fame!
                        </div>
                        @endif
                    </div>

                </div>

            </div>

        </div>

        {{-- Embedded Jarvis Voice CFO --}}
        <x-jarvis-voice />
    </div>

    <script>
    function impulseCalculator(monthlyIncome) {
        return {
            income: monthlyIncome || 50000,
            itemName: '',
            price: '',

            calcWorkHours() {
                if (!this.price) return 0;
                const hourlyWage = Math.max(100, this.income / 176);
                return (this.price / hourlyWage).toFixed(1);
            },

            calcWorkDays() {
                const hours = parseFloat(this.calcWorkHours()) || 0;
                return (hours / 8).toFixed(1);
            },

            calcFutureValue() {
                if (!this.price) return 0;
                return Math.round(this.price * Math.pow(1 + 0.12, 10));
            }
        };
    }
    </script>
</x-app-layout>
