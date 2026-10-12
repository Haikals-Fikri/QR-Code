@php
    $admin = Auth::user()->isAdmin();
@endphp

<aside x-data="{ open: false }" class="relative">
    {{-- Overlay mobile --}}
    <div x-show="open" x-transition.opacity @click="open = false"
         class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden" style="display:none"></div>

    {{-- Sidebar --}}
    <div :class="open ? 'translate-x-0' : '-translate-x-full'"
         class="sidebar fixed inset-y-0 left-0 z-50 w-64 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0">

        {{-- Logo --}}
        <div class="h-16 flex items-center gap-2.5 px-5 border-b border-white/5 shrink-0">
            <x-application-logo class="h-8 w-8 shrink-0" />
            <div class="leading-tight">
                <p class="font-bold text-white text-sm">QR-Code System</p>
                <p class="text-[11px] text-slate-500">{{ $admin ? 'Panel Admin' : 'Panel Siswa' }}</p>
            </div>
            <button @click="open = false" class="ms-auto text-slate-400 hover:text-white lg:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 overflow-y-auto px-3 py-3 space-y-1">
            @if ($admin)
                <p class="sidebar-section">Menu Utama</p>
                <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                    :icon="'<svg class=\'h-5 w-5\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.7\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6\'/></svg>'">
                    Dasbor
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.classes.index')" :active="request()->routeIs('admin.classes.*')"
                    :icon="'<svg class=\'h-5 w-5\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.7\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.42A12 12 0 0112 21a12 12 0 01-6.16-10.42L12 14z\'/></svg>'">
                    Kelas
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')"
                    :icon="'<svg class=\'h-5 w-5\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.7\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M17 20h5v-2a3 3 0 00-5.36-1.86M17 20H7m10 0v-2c0-.65-.13-1.28-.36-1.86m0 0a5.97 5.97 0 00-3.64-1.24M7 20H2v-2a3 3 0 015.36-1.86M7 20v-2c0-.65.13-1.28.36-1.86m0 0A6 6 0 0112 14a6 6 0 014.64 2.14M15 7a3 3 0 11-6 0 3 3 0 016 0z\'/></svg>'">
                    Siswa
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.schedules.index')" :active="request()->routeIs('admin.schedules.*') || request()->routeIs('admin.sessions.*')"
                    :icon="'<svg class=\'h-5 w-5\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.7\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg>'">
                    Jadwal &amp; QR
                </x-sidebar-link>

                <p class="sidebar-section">Analitik</p>
                <x-sidebar-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')"
                    :icon="'<svg class=\'h-5 w-5\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.7\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z\'/></svg>'">
                    Laporan
                </x-sidebar-link>
            @else
                <p class="sidebar-section">Menu</p>
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    :icon="'<svg class=\'h-5 w-5\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.7\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6\'/></svg>'">
                    Dasbor
                </x-sidebar-link>
                <x-sidebar-link :href="route('scan.index')" :active="request()->routeIs('scan.*')"
                    :icon="'<svg class=\'h-5 w-5\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.7\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' d=\'M3 9V5a2 2 0 012-2h4M15 3h4a2 2 0 012 2v4M21 15v4a2 2 0 01-2 2h-4M9 21H5a2 2 0 01-2-2v-4M3 12h18\'/></svg>'">
                    Scan QR
                </x-sidebar-link>
            @endif
        </nav>

        {{-- Profil + logout di bawah --}}
        <div class="border-t border-white/5 p-3 shrink-0">
            <div class="flex items-center gap-3 px-3 py-2">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="mt-1 space-y-1">
                <a href="{{ route('profile.edit') }}" class="sidebar-link">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="sidebar-link"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </a>
                </form>
            </div>
        </div>
    </div>
</aside>
