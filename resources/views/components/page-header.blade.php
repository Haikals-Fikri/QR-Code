@props([
    'title',
    'subtitle' => null,
])

<div>
    <h1 class="text-lg font-bold text-gray-900 leading-tight truncate">{{ $title }}</h1>
    @if ($subtitle)
        <p class="text-xs text-gray-500 truncate">{{ $subtitle }}</p>
    @endif
</div>
