@props([
 'variant' => 'default',
 'size' => 'default',
 'as' => 'button',
 'href' => null,
])

@php
 $baseClasses = 'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';

 $variants = [
 'default' => ' text-slate-50 hover::',
 'destructive' => 'bg-red-500 text-slate-50 hover:bg-red-500/90:bg-red-900/90',
 'outline' => 'border border-slate-200 bg-white hover: hover:text-slate-900::text-slate-50',
 'secondary' => ' text-slate-900 hover::',
 'ghost' => 'hover: hover:text-slate-900::text-slate-50',
 'link' => 'text-slate-900 underline-offset-4 hover:underline',
 ];

 $sizes = [
 'default' => 'h-10 px-4 py-2',
 'sm' => 'h-9 rounded-md px-3',
 'lg' => 'h-11 rounded-md px-8',
 'icon' => 'h-10 w-10',
 ];

 $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['default']);
 
 // Determine the HTML tag to use
 $tag = $as === 'a' || $href ? 'a' : 'button';
@endphp

<{{ $tag }} 
 @if($tag === 'a') href="{{ $href }}" @endif
 {{ $attributes->merge(['class' => $classes]) }}
>
 {{ $slot }}
</{{ $tag }}>
