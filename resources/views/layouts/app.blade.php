<x-layouts.base>
    @php
        $currentRoute = request()->route()->getName();
    @endphp

    @if (in_array($currentRoute, ['employeeDashboard']))
        {{-- Nav --}}
        @include('components.layouts.nav')

        {{-- SideNav --}}
        @include('components.layouts.sidenav')

        <main class="content">
            {{-- TopBar --}}
            @include('components.layouts.topbar')

            {{ $slot }}

            {{-- Footer --}}
            {{-- @include('components.layouts.footer') --}}
        </main>
    @elseif(in_array($currentRoute, ['login', 'register', 'forgot-password', 'reset-password']))
        {{-- Just show the content without nav and sidenav --}}
        {{ $slot }}
    @else
        {{-- Any other route (e.g., errors like 404 or 500) --}}
        {{ $slot }}
    @endif
</x-layouts.base>
