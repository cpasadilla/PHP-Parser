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

            @if(isset($ships) && $ships->isNotEmpty())
                @foreach($ships as $ship)
                    @if(Auth::user()->hasSubpagePermission('masterlist', 'voyage'))
                    <x-sidebar.sublink
                        title="M/V Everwin Star {{ $ship->ship_number }}"
                        href="{{ route('masterlist.voyage', ['id' => $ship->id]) }}"
                        :active="request()->routeIs('masterlist.voyage') && request()->ship == $ship->id"
                    />
                    @endif
                @endforeach
            @endif

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
    <x-sidebar.dropdown
        title="Accounting"
        :active="Str::startsWith(request()->route()->uri(), 'accounting')"
        >
            <x-slot name="icon">
                <x-heroicon-o-calculator class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>
            
            <!-- Daily Cash Collection Reports -->
            @if (Auth::user()->hasSubpagePermission('accounting', 'daily-cash-collection'))
            <x-sidebar.sublink
                title="Daily Cash Collection Report (Trading)"
                href="{{ route('accounting.daily-cash-collection.trading') }}"
                :active="request()->routeIs('accounting.daily-cash-collection.trading')"
            />
            <x-sidebar.sublink
                title="Daily Cash Collection Report (Shipping)"
                href="{{ route('accounting.daily-cash-collection.shipping') }}"
                :active="request()->routeIs('accounting.daily-cash-collection.shipping')"
            />
            @endif

            <!-- Monthly Cash Receipt Journals -->
            @if (Auth::user()->hasSubpagePermission('accounting', 'monthly-cash-receipt'))
            <x-sidebar.sublink
                title="Monthly Cash Receipt Journal (Trading)"
                href="{{ route('accounting.monthly-cash-receipt.trading') }}"
                :active="request()->routeIs('accounting.monthly-cash-receipt.trading')"
            />
            <x-sidebar.sublink
                title="Monthly Cash Receipt Journal (Shipping)"
                href="{{ route('accounting.monthly-cash-receipt.shipping') }}"
                :active="request()->routeIs('accounting.monthly-cash-receipt.shipping')"
            />
            @endif

            <!-- Financial Statements Trading -->
            @if (Auth::user()->hasSubpagePermission('accounting', 'financial-statement-trading'))
            <x-sidebar.subdropdown
                title="Financial Statement (Trading)"
                :active="Str::contains(request()->route()->uri(), 'financial-statement/trading')"
                >
                <x-sidebar.subsublink
                    title="Pre-Trial Balance"
                    href="{{ route('accounting.financial-statement.trading.pre-trial-balance') }}"
                    :active="request()->routeIs('accounting.financial-statement.trading.pre-trial-balance')"
                />
                <x-sidebar.subsublink
                    title="Trial Balance"
                    href="{{ route('accounting.financial-statement.trading.trial-balance') }}"
                    :active="request()->routeIs('accounting.financial-statement.trading.trial-balance')"
                />
                <x-sidebar.subsublink
                    title="Balance Sheet"
                    href="{{ route('accounting.financial-statement.trading.balance-sheet') }}"
                    :active="request()->routeIs('accounting.financial-statement.trading.balance-sheet')"
                />
                <x-sidebar.subsublink
                    title="Statement of Income and Expenses"
                    href="{{ route('accounting.financial-statement.trading.income-statement') }}"
                    :active="request()->routeIs('accounting.financial-statement.trading.income-statement')"
                />
                <x-sidebar.subsublink
                    title="Work Sheet"
                    href="{{ route('accounting.financial-statement.trading.work-sheet') }}"
                    :active="request()->routeIs('accounting.financial-statement.trading.work-sheet')"
                />
                <x-sidebar.subsublink
                    title="Working Trial Balance"
                    href="{{ route('accounting.financial-statement.trading.working-trial-balance') }}"
                    :active="request()->routeIs('accounting.financial-statement.trading.working-trial-balance')"
                />
            </x-sidebar.subdropdown>
            @endif

            <!-- Financial Statements Shipping -->
            @if (Auth::user()->hasSubpagePermission('accounting', 'financial-statement-shipping'))
            <x-sidebar.subdropdown
                title="Financial Statement (Shipping)"
                :active="Str::contains(request()->route()->uri(), 'financial-statement/shipping')"
                >
                <x-sidebar.subsublink
                    title="Pre-Trial Balance"
                    href="{{ route('accounting.financial-statement.shipping.pre-trial-balance') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.pre-trial-balance')"
                />
                <x-sidebar.subsublink
                    title="Trial Balance"
                    href="{{ route('accounting.financial-statement.shipping.trial-balance') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.trial-balance')"
                />
                <x-sidebar.subsublink
                    title="Balance Sheet"
                    href="{{ route('accounting.financial-statement.shipping.balance-sheet') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.balance-sheet')"
                />
                <x-sidebar.subsublink
                    title="Statement of Income and Expenses"
                    href="{{ route('accounting.financial-statement.shipping.income-statement') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.income-statement')"
                />
                <x-sidebar.subsublink
                    title="Administrative Expenses"
                    href="{{ route('accounting.financial-statement.shipping.admin-expenses') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.admin-expenses')"
                />
                <x-sidebar.subsublink
                    title="Everwin Star I"
                    href="{{ route('accounting.financial-statement.shipping.everwin-star-1') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.everwin-star-1')"
                />
                <x-sidebar.subsublink
                    title="Everwin Star II"
                    href="{{ route('accounting.financial-statement.shipping.everwin-star-2') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.everwin-star-2')"
                />
                <x-sidebar.subsublink
                    title="Everwin Star III"
                    href="{{ route('accounting.financial-statement.shipping.everwin-star-3') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.everwin-star-3')"
                />
                <x-sidebar.subsublink
                    title="Everwin Star IV"
                    href="{{ route('accounting.financial-statement.shipping.everwin-star-4') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.everwin-star-4')"
                />
                <x-sidebar.subsublink
                    title="Everwin Star V"
                    href="{{ route('accounting.financial-statement.shipping.everwin-star-5') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.everwin-star-5')"
                />
                <x-sidebar.subsublink
                    title="Work Sheet"
                    href="{{ route('accounting.financial-statement.shipping.work-sheet') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.work-sheet')"
                />
                <x-sidebar.subsublink
                    title="Working Trial Balance"
                    href="{{ route('accounting.financial-statement.shipping.working-trial-balance') }}"
                    :active="request()->routeIs('accounting.financial-statement.shipping.working-trial-balance')"
                />
            </x-sidebar.subdropdown>
            @endif

            <!-- General Journal -->
            @if (Auth::user()->hasSubpagePermission('accounting', 'general-journal'))
            <x-sidebar.subdropdown
                title="General Journal"
                :active="Str::contains(request()->route()->uri(), 'general-journal')"
                >
                <x-sidebar.subsublink
                    title="Fully Depreciated PPE"
                    href="{{ route('accounting.general-journal.fully-depreciated-ppe') }}"
                    :active="request()->routeIs('accounting.general-journal.fully-depreciated-ppe')"
                />
                <x-sidebar.subsublink
                    title="Schedule of Depreciation Expenses"
                    href="{{ route('accounting.general-journal.schedule-depreciation') }}"
                    :active="request()->routeIs('accounting.general-journal.schedule-depreciation')"
                />
                <x-sidebar.subsublink
                    title="General Journal"
                    href="{{ route('accounting.general-journal.index') }}"
                    :active="request()->routeIs('accounting.general-journal.index')"
                />
                <x-sidebar.subsublink
                    title="Check Disbursement Journal (Trading)"
                    href="{{ route('accounting.general-journal.check-disbursement.trading') }}"
                    :active="request()->routeIs('accounting.general-journal.check-disbursement.trading')"
                />
                <x-sidebar.subsublink
                    title="Check Disbursement Journal (Shipping)"
                    href="{{ route('accounting.general-journal.check-disbursement.shipping') }}"
                    :active="request()->routeIs('accounting.general-journal.check-disbursement.shipping')"
                />
                <x-sidebar.subsublink
                    title="Cash Disbursement Journal"
                    href="{{ route('accounting.general-journal.cash-disbursement') }}"
                    :active="request()->routeIs('accounting.general-journal.cash-disbursement')"
                />
            </x-sidebar.subdropdown>
            @endif

            <!-- Additional Accounting Reports -->
            @if (Auth::user()->hasSubpagePermission('accounting', 'additional-reports'))
            <x-sidebar.sublink
                title="Breakdown of Receivables"
                href="{{ route('accounting.breakdown-of-receivables') }}"
                :active="request()->routeIs('accounting.breakdown-of-receivables')"
            />
            <x-sidebar.sublink
                title="Cash on Hand Register"
                href="{{ route('accounting.cash-on-hand-register') }}"
                :active="request()->routeIs('accounting.cash-on-hand-register')"
            />
            @endif
    </x-sidebar.dropdown>
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

    @if(Auth::user()->hasPagePermission('crew') || Auth::user()->hasPermission('crew', 'access'))
    <x-sidebar.dropdown
        title="Crew Management"
        :active="Str::startsWith(request()->route()->uri(), 'crew') || 
                 Str::startsWith(request()->route()->uri(), 'leave-applications') ||
                 Str::startsWith(request()->route()->uri(), 'leave-credits') ||
                 Str::startsWith(request()->route()->uri(), 'crew-documents')"
        >
            <x-slot name="icon">
                <x-heroicon-o-user-group class="flex-shrink-0 w-6 h-6" aria-hidden="true" />
            </x-slot>

            @if(Auth::user()->hasPagePermission('crew') || Auth::user()->hasPermission('crew', 'access') || Auth::user()->hasSubpagePermission('crew', 'crew-management', 'access'))
            <x-sidebar.sublink
                title="Crew List"
                href="{{ route('crew.index') }}"
                :active="request()->routeIs('crew.*')"
            />
            @endif

            @if(Auth::user()->hasPagePermission('crew-documents') || Auth::user()->hasPermission('crew', 'access') || Auth::user()->hasSubpagePermission('crew', 'document-management', 'access'))
            <x-sidebar.sublink
                title="Document Management"
                href="{{ route('crew-documents.index') }}"
                :active="request()->routeIs('crew-documents.*')"
            />
            @endif

            @if(Auth::user()->hasPagePermission('crew-documents') || Auth::user()->hasPermission('crew', 'access') || Auth::user()->hasSubpagePermission('crew', 'expiring-documents', 'access'))
            <x-sidebar.sublink
                title="Expiring Documents"
                href="{{ route('crew-documents.expiring') }}"
                :active="request()->routeIs('crew-documents.expiring')"
            />
            @endif

            @if(Auth::user()->hasPagePermission('leave-applications') || Auth::user()->hasPermission('crew', 'access') || Auth::user()->hasSubpagePermission('crew', 'leave-applications', 'access'))
            <x-sidebar.sublink
                title="Leave Applications"
                href="{{ route('leave-applications.index') }}"
                :active="request()->routeIs('leave-applications.*') && !request()->routeIs('leave-applications.upload-sick-leave')"
            />
            @endif

            @if(Auth::user()->hasPagePermission('leave-applications') || Auth::user()->hasPermission('crew', 'access') || Auth::user()->hasSubpagePermission('crew', 'upload-sick-leave', 'access'))
            <x-sidebar.sublink
                title="Upload Sick Leave Form"
                href="{{ route('leave-applications.upload-sick-leave') }}"
                :active="request()->routeIs('leave-applications.upload-sick-leave')"
            />
            @endif

            @if(Auth::user()->hasPagePermission('leave-applications') || Auth::user()->hasPermission('crew', 'access') || Auth::user()->hasSubpagePermission('crew', 'leave-credits', 'access'))
            <x-sidebar.sublink
                title="Leave Credits Management"
                href="{{ route('leave-credits.index') }}"
                :active="request()->routeIs('leave-credits.*')"
            />
            @endif
    </x-sidebar.dropdown>
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
