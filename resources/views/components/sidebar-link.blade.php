@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}"
   class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
    @if ($icon)
        <span class="shrink-0">{!! $icon !!}</span>
    @endif
    <span>{{ $slot }}</span>
</a>
