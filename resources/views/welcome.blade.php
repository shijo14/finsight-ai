<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'FinSight AI') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <header class="border-b border-white/10 bg-slate-950/90 backdrop-blur">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="text-xl font-semibold tracking-tight text-white">FinSight AI</a>
                <div class="flex items-center gap-3 text-sm">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-full bg-white px-4 py-2 font-semibold text-slate-900 hover:bg-slate-200">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-white/15 px-4 py-2 text-white hover:bg-white/5">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="rounded-full bg-emerald-400 px-4 py-2 font-semibold text-slate-950 hover:bg-emerald-300">Start free</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>
        </header>

        <main class="mx-auto flex max-w-7xl flex-col gap-16 px-6 py-12 lg:px-8 lg:py-16">
            <section class="grid items-center gap-10 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="space-y-6">
                    <p class="inline-flex rounded-full border border-emerald-400/30 bg-emerald-400/10 px-4 py-1 text-sm text-emerald-200">A modern finance companion for ambitious teams and individuals</p>
                    <h1 class="max-w-xl text-4xl font-bold tracking-tight text-white lg:text-6xl">Track your money, plan your future, and grow with confidence.</h1>
                    <p class="max-w-2xl text-lg text-slate-300">FinSight AI helps you manage income, expenses, budgets, goals, and investments in one polished workspace designed to feel premium from day one.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" class="rounded-full bg-emerald-400 px-6 py-3 font-semibold text-slate-950 hover:bg-emerald-300">Create your account</a>
                        <a href="{{ route('login') }}" class="rounded-full border border-white/15 px-6 py-3 font-semibold text-white hover:bg-white/5">See the dashboard</a>
                    </div>
                    <dl class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><dt class="text-sm text-slate-300">Smart Insights</dt><dd class="mt-2 text-2xl font-semibold text-white">AI-ready</dd></div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><dt class="text-sm text-slate-300">Budget Control</dt><dd class="mt-2 text-2xl font-semibold text-white">Real-time</dd></div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><dt class="text-sm text-slate-300">Goal Tracking</dt><dd class="mt-2 text-2xl font-semibold text-white">Always on</dd></div>
                    </dl>
                </div>

                <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-6 shadow-2xl shadow-emerald-500/10">
                    <div class="rounded-2xl border border-emerald-400/20 bg-slate-950/70 p-5">
                        <p class="text-sm text-emerald-200">Live overview</p>
                        <div class="mt-4 space-y-4 text-sm text-slate-200">
                            <div class="flex items-center justify-between rounded-xl bg-white/5 p-4"><span>Income</span><strong class="text-emerald-300">+$4,800</strong></div>
                            <div class="flex items-center justify-between rounded-xl bg-white/5 p-4"><span>Expenses</span><strong class="text-rose-300">-$2,190</strong></div>
                            <div class="flex items-center justify-between rounded-xl bg-white/5 p-4"><span>Net balance</span><strong class="text-white">+$2,610</strong></div>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <article class="rounded-2xl border border-white/10 bg-white/5 p-4"><h2 class="text-sm text-slate-300">Budget planner</h2><p class="mt-2 text-xl font-semibold text-white">Stay within your limits</p></article>
                        <article class="rounded-2xl border border-white/10 bg-white/5 p-4"><h2 class="text-sm text-slate-300">Investment insight</h2><p class="mt-2 text-xl font-semibold text-white">See opportunities grow</p></article>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 md:grid-cols-3">
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <h2 class="text-xl font-semibold text-white">Income &amp; Expenses</h2>
                    <p class="mt-2 text-slate-300">Capture every source of cash flow and keep every spending category in view.</p>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <h2 class="text-xl font-semibold text-white">Goals &amp; Budgets</h2>
                    <p class="mt-2 text-slate-300">Set milestone targets and build practical plans for savings, debt payoff, and investing.</p>
                </article>
                <article class="rounded-3xl border border-white/10 bg-white/5 p-6">
                    <h2 class="text-xl font-semibold text-white">AI Assistant</h2>
                    <p class="mt-2 text-slate-300">Ask for quick financial guidance and get clear next steps from your personal workspace.</p>
                </article>
            </section>
        </main>
    </body>
</html>
