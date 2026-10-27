<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FinSight AI — Your Modern Finance Companion</title>

        <x-theme-init />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,700|figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-neutral-50 dark:bg-finsight-gradient text-neutral-900 dark:text-slate-100 antialiased">
        <header class="glass-nav sticky top-0 z-50">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-neutral-900 dark:text-white">
                    FinSight <span class="text-cyan-600 dark:text-cyan-400">AI</span>
                </a>
                <div class="flex items-center gap-3 text-sm">
                    <x-theme-toggle compact />
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary">Start free</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>
        </header>

        <main>
            {{-- Slide 1: Hero --}}
            <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-28">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div class="space-y-8 animate-slide-up">
                        <p class="section-label">AI-Powered Finance</p>
                        <h1 class="text-5xl font-bold tracking-tight text-neutral-900 dark:text-white lg:text-7xl">
                            FinSight <span class="text-cyan-400">AI</span>
                        </h1>
                        <p class="text-2xl font-medium text-neutral-700 dark:text-slate-300">Your Modern Finance Companion</p>
                        <p class="max-w-xl text-lg text-neutral-500 dark:text-slate-400">
                            A premium, AI-driven platform for ambitious individuals and teams — turning financial chaos into clarity, confidence, and growth.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('register') }}" class="btn-primary px-8 py-3 text-base">Create your account</a>
                            <a href="{{ route('login') }}" class="btn-secondary px-8 py-3 text-base">See the dashboard</a>
                        </div>
                    </div>

                    <div class="glass-card p-6 animate-fade-in">
                        <p class="text-sm text-neutral-500 dark:text-slate-400">Live pulse · this month</p>
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center justify-between rounded-xl bg-neutral-100 dark:bg-white/5 p-4 border border-neutral-200 dark:border-white/10">
                                <div>
                                    <span class="text-xs text-neutral-400 dark:text-slate-500">Salary + Freelance</span>
                                    <p class="text-neutral-700 dark:text-slate-300 text-sm font-medium">Income</p>
                                </div>
                                <strong class="text-emerald-500 dark:text-emerald-400 text-lg" id="hero-income">+₹85,000</strong>
                            </div>
                            <div class="flex items-center justify-between rounded-xl bg-neutral-100 dark:bg-white/5 p-4 border border-neutral-200 dark:border-white/10">
                                <div>
                                    <span class="text-xs text-neutral-400 dark:text-slate-500">SIP · Rent · Zomato</span>
                                    <p class="text-neutral-700 dark:text-slate-300 text-sm font-medium">Expenses</p>
                                </div>
                                <strong class="text-red-500 dark:text-red-400 text-lg" id="hero-expenses">-₹32,400</strong>
                            </div>
                            <div class="flex items-center justify-between rounded-xl bg-cyan-400/10 p-4 border border-cyan-400/30">
                                <div>
                                    <span class="text-xs text-cyan-600 dark:text-cyan-500">Savings rate: 61.9%</span>
                                    <p class="text-cyan-700 dark:text-cyan-300 text-sm font-medium">Net balance</p>
                                </div>
                                <strong class="text-cyan-600 dark:text-cyan-300 text-2xl font-bold" id="hero-balance">+₹52,600</strong>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-neutral-400 dark:text-slate-500 text-center">✦ AI-powered forecast: +₹58,200 next month</p>
                    </div>
                </div>
            </section>

            {{-- Slide 2: The Problem --}}
            <section class="border-y border-neutral-200 dark:border-white/10 bg-neutral-100 dark:bg-black/20 py-20">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <p class="section-label">The Problem</p>
                        <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white lg:text-4xl">Financial Management Shouldn't Feel Like This</h2>
                        <p class="mx-auto mt-4 max-w-3xl text-neutral-500 dark:text-slate-400">
                            Traditional tools are clunky, fragmented, and passive. You're left piecing together income, expenses, and goals across disconnected apps — with no real guidance on what to do next.
                        </p>
                    </div>
                    <div class="grid gap-6 md:grid-cols-3">
                        <article class="glass-card-hover p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Fragmented Data</h3>
                            <p class="mt-2 text-neutral-500 dark:text-slate-400">Income, expenses, and goals scattered across tools</p>
                        </article>
                        <article class="glass-card-hover p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">No Real Guidance</h3>
                            <p class="mt-2 text-neutral-500 dark:text-slate-400">Numbers shown, but never explained or acted upon</p>
                        </article>
                        <article class="glass-card-hover p-6">
                            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Outdated Experience</h3>
                            <p class="mt-2 text-neutral-500 dark:text-slate-400">Clunky interfaces that feel like a chore to use</p>
                        </article>
                    </div>
                </div>
            </section>

            {{-- Slide 3: The Solution --}}
            <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="text-center mb-12">
                    <p class="section-label">The Solution</p>
                    <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white lg:text-4xl">One Platform. Total Financial Clarity.</h2>
                    <p class="mx-auto mt-4 max-w-3xl text-neutral-500 dark:text-slate-400">
                        FinSight AI centralizes everything — income, expenses, budgets, goals, and investments — into a single, intelligent workspace. Powered by a built-in AI Assistant, it doesn't just show your numbers. It helps you understand and act on them.
                    </p>
                </div>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <article class="glass-card-hover p-6 text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-400/10 border border-cyan-400/30">
                            <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="font-semibold text-neutral-900 dark:text-white">Real-Time Dashboard</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Live pulse on your net balance and cash flow</p>
                    </article>
                    <article class="glass-card-hover p-6 text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-400/10 border border-cyan-400/30">
                            <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="font-semibold text-neutral-900 dark:text-white">Smart Budgeting</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Practical budgets that keep you on track</p>
                    </article>
                    <article class="glass-card-hover p-6 text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-400/10 border border-cyan-400/30">
                            <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <h3 class="font-semibold text-neutral-900 dark:text-white">AI Financial Advisor</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Actionable guidance tailored to your data</p>
                    </article>
                    <article class="glass-card-hover p-6 text-center">
                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-400/10 border border-cyan-400/30">
                            <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h3 class="font-semibold text-neutral-900 dark:text-white">Investment Insights</h3>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Track portfolio growth and spot opportunities</p>
                    </article>
                </div>
            </section>

            {{-- Slides 4-7: Features --}}
            <section class="border-y border-neutral-200 dark:border-white/10 bg-neutral-100 dark:bg-black/20 py-20">
                <div class="mx-auto max-w-7xl px-6 lg:px-8 space-y-16">
                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <div>
                            <p class="section-label">Feature 01</p>
                            <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white">Comprehensive Cash Flow Tracking</h2>
                            <p class="mt-4 text-neutral-500 dark:text-slate-400">Every source of income and every expense is captured, categorized, and visualized in real time. No more guessing where your money went — FinSight AI gives you a live, accurate pulse on your financial health.</p>
                        </div>
                        <div class="grid gap-4">
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Multi-Source Income</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Track salary, freelance, side hustles, and more</p></div>
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Smart Categorization</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Expenses auto-sorted for instant clarity</p></div>
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Net Balance View</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Always know exactly where you stand</p></div>
                        </div>
                    </div>

                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <div class="order-2 lg:order-1 grid gap-4">
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Category-level limits</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Set spending limits that fit your lifestyle</p></div>
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Real-time alerts</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Get notified as you approach thresholds</p></div>
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Historical trends</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Review patterns to plan smarter</p></div>
                        </div>
                        <div class="order-1 lg:order-2">
                            <p class="section-label">Feature 02</p>
                            <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white">Smart Budget Planner</h2>
                            <p class="mt-2 text-xl text-neutral-700 dark:text-slate-300">Stay Within Limits — Without Feeling Restricted</p>
                            <p class="mt-4 text-neutral-500 dark:text-slate-400">Build practical, real-time budgets that adapt to your lifestyle. FinSight AI helps you allocate intelligently, flag overspending early, and celebrate when you hit your targets.</p>
                        </div>
                    </div>

                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <div>
                            <p class="section-label">Feature 03</p>
                            <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white">Always-On Goal Tracking</h2>
                            <p class="mt-4 text-neutral-500 dark:text-slate-400">Whether you're saving for a home, paying off debt, or planning a major purchase — FinSight AI keeps your milestones visible, motivating, and within reach.</p>
                        </div>
                        <div class="grid gap-4">
                            <div class="glass-card p-5"><span class="text-2xl">🎯</span><h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Set Milestones</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Define savings, debt payoff, or purchase targets with timelines</p></div>
                            <div class="glass-card p-5"><span class="text-2xl">📊</span><h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Visual Progress</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Watch your progress bars fill in real time</p></div>
                            <div class="glass-card p-5"><span class="text-2xl">🏆</span><h3 class="mt-2 font-semibold text-neutral-900 dark:text-white">Stay Motivated</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Celebrate wins and stay on track with smart nudges</p></div>
                        </div>
                    </div>

                    <div class="grid items-center gap-10 lg:grid-cols-2">
                        <div class="order-2 lg:order-1 grid gap-4">
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Portfolio Overview</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">All holdings in one unified view</p></div>
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Performance Tracking</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">Real-time growth and return metrics</p></div>
                            <div class="glass-card p-5"><h3 class="font-semibold text-neutral-900 dark:text-white">Opportunity Alerts</h3><p class="mt-1 text-sm text-neutral-500 dark:text-slate-400">AI surfaces smart investment ideas</p></div>
                        </div>
                        <div class="order-1 lg:order-2">
                            <p class="section-label">Feature 04</p>
                            <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white">Investment Insights</h2>
                            <p class="mt-2 text-xl text-neutral-700 dark:text-slate-300">Monitor. Analyze. Grow.</p>
                            <p class="mt-4 text-neutral-500 dark:text-slate-400">A dedicated investment tracking module gives you a clear view of your portfolio's performance — helping you identify growth opportunities and make confident, informed decisions.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Slide 8: AI Advisor --}}
            <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                <div class="glass-card p-10 lg:p-14 text-center">
                    <p class="section-label">AI-Powered</p>
                    <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white lg:text-4xl">Your Personal Financial Advisor — Always On</h2>
                    <p class="mx-auto mt-4 max-w-3xl text-neutral-500 dark:text-slate-400">
                        FinSight AI's built-in chat assistant doesn't just answer questions. It analyzes your workspace data to deliver personalized guidance, smart insights, and clear next steps — like having a financial advisor available 24/7.
                    </p>
                    <div class="mt-10 grid gap-6 md:grid-cols-3 text-left">
                        <div class="rounded-xl bg-neutral-100 dark:bg-white/5 p-6 border border-neutral-200 dark:border-white/10">
                            <h3 class="font-semibold text-cyan-300">Ask Anything</h3>
                            <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Natural language questions about your finances</p>
                        </div>
                        <div class="rounded-xl bg-neutral-100 dark:bg-white/5 p-6 border border-neutral-200 dark:border-white/10">
                            <h3 class="font-semibold text-cyan-300">Smart Insights</h3>
                            <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Proactive recommendations based on your data</p>
                        </div>
                        <div class="rounded-xl bg-neutral-100 dark:bg-white/5 p-6 border border-neutral-200 dark:border-white/10">
                            <h3 class="font-semibold text-cyan-300">Actionable Steps</h3>
                            <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Clear, specific actions to improve your financial health</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Slide 9: Experience --}}
            <section class="border-y border-neutral-200 dark:border-white/10 bg-neutral-100 dark:bg-black/20 py-20">
                <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
                    <p class="section-label">Experience</p>
                    <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white lg:text-4xl">A UI That Feels as Premium as Your Ambitions</h2>
                    <p class="mx-auto mt-4 max-w-3xl text-neutral-500 dark:text-slate-400">
                        FinSight AI is built to impress and effortless to use. A glassmorphism-inspired dark mode interface with real-time updates and dynamic micro-animations makes managing your finances genuinely enjoyable.
                    </p>
                    <div class="mt-12 grid gap-6 md:grid-cols-3">
                        <article class="glass-card-hover p-6">
                            <h3 class="font-semibold text-neutral-900 dark:text-white">Glassmorphism Dark Mode</h3>
                            <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Sleek, modern aesthetic that reduces eye strain</p>
                        </article>
                        <article class="glass-card-hover p-6">
                            <h3 class="font-semibold text-neutral-900 dark:text-white">Real-Time Updates</h3>
                            <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Data refreshes instantly — no manual syncing</p>
                        </article>
                        <article class="glass-card-hover p-6">
                            <h3 class="font-semibold text-neutral-900 dark:text-white">Dynamic Micro-Animations</h3>
                            <p class="mt-2 text-sm text-neutral-500 dark:text-slate-400">Subtle motion that makes every interaction feel alive</p>
                        </article>
                    </div>
                </div>
            </section>

            {{-- AI Demo Chat Section --}}
            <section class="mx-auto max-w-7xl px-6 py-20 lg:px-8 scroll-fade">
                <div class="text-center mb-12">
                    <p class="section-label">AI in Action</p>
                    <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white lg:text-4xl">Just Type. FinSight AI Does the Rest.</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-neutral-500 dark:text-slate-400">No forms. No clicking through menus. Tell the AI what happened and it logs it instantly.</p>
                </div>
                <div class="max-w-2xl mx-auto glass-card rounded-3xl overflow-hidden">
                    <div class="px-5 py-3 border-b border-neutral-200 dark:border-white/10 flex items-center gap-3 bg-neutral-100 dark:bg-white/5">
                        <div class="h-8 w-8 rounded-full bg-cyan-500/20 border border-cyan-400/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-sm font-semibold text-neutral-800 dark:text-white">FinSight AI Advisor</span>
                        <span class="ml-auto text-xs text-emerald-500 font-medium">● Online</span>
                    </div>
                    <div class="p-6 space-y-4 bg-neutral-50 dark:bg-white/[0.02]">
                        <div class="flex justify-end">
                            <div class="max-w-[80%] rounded-2xl rounded-tr-sm px-4 py-3 bg-cyan-500 text-slate-950 text-sm font-medium">
                                Add Rs 1,500 petrol expense today
                            </div>
                        </div>
                        <div class="flex justify-start">
                            <div class="max-w-[85%] rounded-2xl rounded-tl-sm px-4 py-3 glass-card text-sm text-neutral-700 dark:text-slate-300">
                                ✅ Done! Expense of <strong>Rs 1,500</strong> under <strong>Petrol</strong> logged for today. Your Transport budget is now at 62% usage — you have Rs 950 remaining this month.
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <div class="max-w-[80%] rounded-2xl rounded-tr-sm px-4 py-3 bg-cyan-500 text-slate-950 text-sm font-medium">
                                Create Rs 8,000 budget for dining
                            </div>
                        </div>
                        <div class="flex justify-start">
                            <div class="max-w-[85%] rounded-2xl rounded-tl-sm px-4 py-3 glass-card text-sm text-neutral-700 dark:text-slate-300">
                                ✅ Budget created! <strong>Rs 8,000</strong> limit for <strong>Dining</strong> this month. Based on your past 3 months, you typically spend Rs 6,200 on dining — this budget gives you a healthy Rs 1,800 buffer.
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-3 border-t border-neutral-200 dark:border-white/10 bg-white/50 dark:bg-slate-900/50">
                        <div class="flex gap-2 items-center text-sm text-neutral-400 dark:text-slate-500">
                            <span class="flex-1">Try: "Add Rs 85,000 salary income" or ask "How am I doing this month?"</span>
                            <a href="{{ route('register') }}" class="btn-primary px-4 py-2 text-xs whitespace-nowrap">Try it free →</a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Slide 10: Built For You + CTA --}}
            <section class="border-t border-neutral-200 dark:border-white/10 mx-auto max-w-7xl px-6 py-20 lg:px-8 scroll-fade">
                <div class="text-center mb-12">
                    <p class="section-label">Built For You</p>
                    <h2 class="mt-3 text-3xl font-bold text-neutral-900 dark:text-white lg:text-4xl">Who FinSight AI Is For</h2>
                </div>
                <div class="grid gap-6 md:grid-cols-3 mb-16">
                    <article class="glass-card-hover p-6 scroll-fade">
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Salaried Professionals</h3>
                        <p class="mt-2 text-neutral-500 dark:text-slate-400">Track salary, SIPs, EMIs, and goals in one place. Know exactly how much you're actually saving every month.</p>
                    </article>
                    <article class="glass-card-hover p-6 scroll-fade">
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Freelancers &amp; Creators</h3>
                        <p class="mt-2 text-neutral-500 dark:text-slate-400">Manage variable income, track UPI/project payments, and plan for lean months with AI-powered cash flow forecasts.</p>
                    </article>
                    <article class="glass-card-hover p-6 scroll-fade">
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Small Business Owners</h3>
                        <p class="mt-2 text-neutral-500 dark:text-slate-400">Monitor revenue, operational costs, competitor rents, and team budgets — all from one intelligent dashboard.</p>
                    </article>
                </div>

                <div class="glass-card p-10 text-center border-cyan-400/20 scroll-fade">
                    <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">Ready to take control of your finances?</h2>
                    <p class="mt-3 text-neutral-500 dark:text-slate-400">Join the smarter way to manage money in India. Free to start, powerful from day one.</p>
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        <a href="{{ route('register') }}" class="btn-primary px-8 py-3 text-base">Get started free</a>
                        <a href="{{ route('login') }}" class="btn-secondary px-8 py-3 text-base">Log in</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-neutral-200 dark:border-white/10 py-8 text-center text-sm text-neutral-500 dark:text-slate-500">
            &copy; {{ date('Y') }} FinSight AI. Your Modern Finance Companion.
        </footer>
    </body>

    <style>
        .scroll-fade { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .scroll-fade.visible { opacity: 1; transform: translateY(0); }
    </style>

    <script>
        // Scroll-triggered fade-in
        const fadeSections = document.querySelectorAll('.scroll-fade');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), i * 80);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        fadeSections.forEach(el => observer.observe(el));
    </script>
</html>
