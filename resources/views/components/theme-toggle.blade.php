@props(['compact' => false])

<div
    x-data="{ dark: document.documentElement.classList.contains('dark') }"
    x-init="window.addEventListener('theme-changed', (e) => { dark = e.detail.theme === 'dark' })"
    {{ $attributes->merge(['class' => 'flex items-center']) }}
>
    @unless($compact)
        <span class="me-2 hidden text-xs font-medium text-neutral-500 dark:text-slate-400 sm:inline" x-text="dark ? 'Dark' : 'Light'"></span>
    @endunless

    <button
        type="button"
        role="switch"
        :aria-checked="dark"
        aria-label="Toggle color theme"
        @click="window.FinSightTheme.toggle(); dark = !dark"
        class="group relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-neutral-300 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-cyan-500/40 focus:ring-offset-2 focus:ring-offset-neutral-50 dark:bg-cyan-500/25 dark:focus:ring-offset-slate-900"
    >
        <span
            aria-hidden="true"
            class="pointer-events-none relative inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out translate-x-0 dark:translate-x-5"
        >
            <span class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity duration-200 ease-in opacity-100 dark:opacity-0">
                <svg class="h-3.5 w-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                </svg>
            </span>
            <span class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity duration-200 ease-in opacity-0 dark:opacity-100">
                <svg class="h-3.5 w-3.5 text-cyan-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                </svg>
            </span>
        </span>
    </button>
</div>
