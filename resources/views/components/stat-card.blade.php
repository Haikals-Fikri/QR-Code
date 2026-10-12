@props([
    'label',
    'value',
    'icon' => null,
    'tone' => 'brand',
    'href' => null,
])

@php
$tones = [
    'brand' => 'bg-brand-50 text-brand-600',
    'emerald' => 'bg-emerald-50 text-emerald-600',
    'amber' => 'bg-amber-50 text-amber-600',
    'purple' => 'bg-purple-50 text-purple-600',
];
$toneClass = $tones[$tone] ?? $tones['brand'];
$tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif
    class="card card-body flex items-center gap-4 {{ $href ? 'hover:shadow-card-hover transition-shadow' : '' }}">
    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $toneClass }}">
        {!! $icon !!}
    </span>
    <div class="min-w-0">
        <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $value }}</p>
        <p class="text-xs font-medium uppercase tracking-wide text-gray-500 truncate">{{ $label }}</p>
    </div>
</{{ $tag }}>
