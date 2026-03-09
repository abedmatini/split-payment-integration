@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-slate-100 text-slate-900'
    : 'inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
