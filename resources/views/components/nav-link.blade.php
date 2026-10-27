@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 pt-1 border-b-2 border-neutral-900 text-sm font-medium leading-5 text-neutral-900 focus:outline-none transition duration-150 ease-in-out dark:border-cyan-400 dark:text-cyan-300'
            : 'inline-flex items-center px-3 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-neutral-500 hover:text-neutral-800 hover:border-neutral-300 focus:outline-none transition duration-150 ease-in-out dark:text-slate-400 dark:hover:text-slate-200 dark:hover:border-white/20';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
