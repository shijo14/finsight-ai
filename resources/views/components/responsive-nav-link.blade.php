@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-neutral-900 text-start text-base font-medium text-neutral-900 bg-neutral-100 focus:outline-none transition duration-150 ease-in-out dark:border-cyan-400 dark:text-cyan-300 dark:bg-white/5'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-neutral-500 hover:text-neutral-800 hover:bg-neutral-50 hover:border-neutral-300 focus:outline-none transition duration-150 ease-in-out dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-white/5 dark:hover:border-white/20';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
