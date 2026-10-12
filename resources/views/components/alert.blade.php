@props(['type' => 'success'])

@php
$styles = [
    'success' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
    'error' => 'bg-red-50 text-red-800 border-red-200',
    'info' => 'bg-brand-50 text-brand-800 border-brand-200',
];
@endphp

<div {{ $attributes->merge(['class' => 'border rounded-lg p-4 text-sm font-medium ' . ($styles[$type] ?? $styles['success'])]) }}>
    {{ $slot }}
</div>
