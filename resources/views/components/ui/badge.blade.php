@props([
 'variant' => 'default',
])

@php
 $baseClasses = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2';

 $variants = [
 'default' => 'border-transparent text-slate-50 hover::',
 'secondary' => 'border-transparent text-slate-900 hover::',
 'destructive' => 'border-transparent bg-red-500 text-slate-50 hover:bg-red-500/80:bg-red-900/80',
 'outline' => 'text-slate-950',
 ];

 $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['default']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
 {{ $slot }}
</div>
