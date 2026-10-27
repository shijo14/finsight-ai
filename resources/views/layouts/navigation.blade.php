<nav x-data="{ open: false }" class="glass-nav sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight text-neutral-900 dark:text-white">
                        FinSight <span class="text-cyan-600 dark:text-cyan-400">AI</span>
                    </a>
                </div>

                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
                        {{ __('Expenses') }}
                    </x-nav-link>
                    <x-nav-link :href="route('incomes.index')" :active="request()->routeIs('incomes.*')">
                        {{ __('Incomes') }}
                    </x-nav-link>
                    <x-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">
                        {{ __('Budgets') }}
                    </x-nav-link>
                    <x-nav-link :href="route('goals.index')" :active="request()->routeIs('goals.*')">
                        {{ __('Goals') }}
                    </x-nav-link>
                    <x-nav-link :href="route('investments.index')" :active="request()->routeIs('investments.*')">
                        {{ __('Investments') }}
                    </x-nav-link>
                    <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('Reports') }}
                    </x-nav-link>
                    <x-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">
                        {{ __('AI Advisor') }}
                    </x-nav-link>
                    <x-nav-link :href="route('time-machine.index')" :active="request()->routeIs('time-machine.*')" class="relative">
                        <span class="inline-flex items-center gap-1 text-cyan-600 dark:text-cyan-400 font-semibold">
                            <span>⏳</span> {{ __('Time Machine') }}
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('impulse-shield.index')" :active="request()->routeIs('impulse-shield.*')" class="relative">
                        <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-semibold">
                            <span>🛡️</span> {{ __('Impulse Shield') }}
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('multiverse.index')" :active="request()->routeIs('multiverse.*')" class="relative">
                        <span class="inline-flex items-center gap-1 text-purple-600 dark:text-purple-400 font-bold">
                            <span>🌌</span> {{ __('Multiverse') }}
                        </span>
                    </x-nav-link>
                    <x-nav-link :href="route('nexus.index')" :active="request()->routeIs('nexus.*')" class="relative">
                        <span class="inline-flex items-center gap-1 text-amber-500 dark:text-amber-400 font-bold">
                            <span>⚡</span> {{ __('Nexus') }}
                        </span>
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-4 sm:ms-6">
                <x-theme-toggle compact />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="nav-user-btn inline-flex items-center px-3 py-2 border border-neutral-200 text-sm leading-4 font-medium rounded-xl text-neutral-700 bg-neutral-100 hover:bg-neutral-200 focus:outline-none transition duration-150 ease-in-out">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center gap-2 sm:hidden">
                <x-theme-toggle compact />

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-neutral-500 hover:text-neutral-900 hover:bg-neutral-100 focus:outline-none transition duration-150 ease-in-out dark:text-slate-400 dark:hover:text-white dark:hover:bg-white/10">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden glass-panel">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">{{ __('Expenses') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('incomes.index')" :active="request()->routeIs('incomes.*')">{{ __('Incomes') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('budgets.index')" :active="request()->routeIs('budgets.*')">{{ __('Budgets') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('goals.index')" :active="request()->routeIs('goals.*')">{{ __('Goals') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('investments.index')" :active="request()->routeIs('investments.*')">{{ __('Investments') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">{{ __('Reports') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">{{ __('AI Advisor') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('time-machine.index')" :active="request()->routeIs('time-machine.*')">
                <span class="text-cyan-600 dark:text-cyan-400 font-semibold">⏳ {{ __('Time Machine Simulator') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('impulse-shield.index')" :active="request()->routeIs('impulse-shield.*')">
                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">🛡️ {{ __('Impulse Shield (48h Vault)') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('multiverse.index')" :active="request()->routeIs('multiverse.*')">
                <span class="text-purple-600 dark:text-purple-400 font-semibold">🌌 {{ __('Financial Multiverse Studio') }}</span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('nexus.index')" :active="request()->routeIs('nexus.*')">
                <span class="text-amber-500 dark:text-amber-400 font-semibold">⚡ {{ __('Zen & Quant Nexus') }}</span>
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-neutral-200 dark:border-white/10">
            <div class="px-4 flex items-center justify-between">
                <div>
                    <div class="font-medium text-base text-neutral-900 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-neutral-500 dark:text-slate-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1 px-2">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
