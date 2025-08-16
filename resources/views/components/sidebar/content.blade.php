<x-perfect-scrollbar
    as="nav"
    aria-label="main"
    class="flex flex-col flex-1 gap-4 px-3"
>

    <x-sidebar.link
        title="Dashboard"
        href="{{ route('dashboard') }}"
        :isActive="request()->routeIs('dashboard')"
        >
            <x-slot name="icon">
                <x-icons.dashboard class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
    </x-sidebar.link>

    @if(Auth::user()->hasPagePermission('customer'))
    <x-sidebar.link
        title="Customer"
        href="{{ route('customer') }}"
        :isActive="request()->routeIs('customer')"
        >
            <x-slot name="icon">
                <x-heroicon-o-user-group class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
    </x-sidebar.link>
    @endif

    @if(Auth::user()->hasPagePermission('users'))
    <x-sidebar.link
        title="Staff"
        href="{{ route('users') }}"
        :isActive="request()->routeIs('users')"
        >
            <x-slot name="icon">
                <x-heroicon-o-users class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
    </x-sidebar.link>
    @endif

    @if(Auth::user()->hasPagePermission('history'))
    <x-sidebar.link
        title="History"
        href="{{ route('history') }}"
        :isActive="request()->routeIs('history')"
        >
            <x-slot name="icon">
                <x-heroicon-o-clock class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
    </x-sidebar.link>
    @endif

    @if(Auth::user()->hasPagePermission('pricelist'))
    <x-sidebar.link
        title="Price List"
        href="{{ route('pricelist') }}"
        :isActive="request()->routeIs('pricelist')"
        >
            <x-slot name="icon">
                <x-heroicon-o-tag class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
    </x-sidebar.link>
    @endif

    @if(Auth::user()->hasPagePermission('masterlist'))
    <x-sidebar.dropdown
        title="Master List"
        :active="Str::startsWith(request()->route()->uri(), 'buttons')"
        >
            <x-slot name="icon">
                <x-heroicon-o-view-list class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
            @if (Auth::user()->hasSubpagePermission('masterlist', 'ships'))
            <x-sidebar.sublink
                title="Ship Status"
                href="{{ route('masterlist') }}"
                :active="request()->routeIs('masterlist')"
            />
            @endif

            @foreach($ships as $ship)
                @if(Auth::user()->hasSubpagePermission('masterlist', 'voyage'))
                <x-sidebar.sublink
                    title="M/V Everwin Star {{ $ship->ship_number }}"
                    href="{{ route('masterlist.voyage', ['id' => $ship->id]) }}"
                    :active="request()->routeIs('masterlist.voyage') && request()->ship == $ship->id"
                />
                @endif
            @endforeach

            @if(Auth::user()->hasSubpagePermission('masterlist', 'customer'))
            <x-sidebar.sublink
                title="Customer"
                href="{{ route('masterlist.customer') }}"
                :active="request()->routeIs('masterlist.customer')"
            />
            @endif

            @if(Auth::user()->hasSubpagePermission('masterlist', 'container'))
            <x-sidebar.sublink
                title="Container"
                href="{{ route('masterlist.container') }}"
                :active="request()->routeIs('masterlist.container')"
            />
            @endif

            @if(Auth::user()->hasSubpagePermission('masterlist', 'parcel'))
            <x-sidebar.sublink
                title="Item"
                href="{{ route('masterlist.parcel') }}"
                :active="request()->routeIs('masterlist.parcel')"
            />
            @endif

            @if(Auth::user()->hasSubpagePermission('masterlist', 'soa'))
            <x-sidebar.sublink
                title="Statement of Account"
                href="{{ route('masterlist.soa') }}"
                :active="request()->routeIs('masterlist.soa')"
            />
            @endif
    </x-sidebar.dropdown>
    @endif

    @if(Auth::user()->hasPagePermission('accounting'))
    <x-sidebar.link
        title="Accounting"
        href="{{ route('accounting') }}"
        :isActive="request()->routeIs('accounting')"
        >
            <x-slot name="icon">
                <x-heroicon-o-calculator class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
    </x-sidebar.link>
    @endif

    @if(Auth::user()->hasPagePermission('inventory'))
    <x-sidebar.link
        title="Inventory"
        href="{{ route('inventory') }}"
        :isActive="request()->routeIs('inventory')"
        >
            <x-slot name="icon">
                <x-heroicon-o-cube class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
    </x-sidebar.link>
    @endif
    
    
    @if(Auth::user()->roles && in_array(strtoupper(trim(Auth::user()->roles->roles)), ['ADMIN', 'ADMINISTRATOR']))
    <div
        x-transition
        x-show="isSidebarOpen || isSidebarHovered"
        class="text-sm text-gray-500"
    >
        Dummy Links
    </div>

    <x-sidebar.link title="Dummy link 1" href="#" />
    @endif

</x-perfect-scrollbar>
