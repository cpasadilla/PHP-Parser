<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight flex items-center gap-2">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Gate Pass') }}
            </h2>
            <a href="{{ route('gatepass.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                Gate Pass List
            </a>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Select a Ship to View Gate Pass Status</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">View release status of all items for each ship and voyage.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($shipsData as $shipNum => $voyages)
                @php
                    $isSaverStar = $shipNum === 'SAVER';
                    $shipName = $isSaverStar ? 'M/V SAVER STAR' : 'M/V EVERWIN STAR';
                @endphp
                
                <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-lg font-bold">{{ $shipName }}</h4>
                        <span class="text-sm text-gray-500">{{ $shipNum }}</span>
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Select Voyage:</p>
                        <div class="space-y-1 max-h-60 overflow-y-auto">
                            @foreach($voyages->sortByDesc(function($voyage) { return (int) $voyage->voyageNum; }) as $voyage)
                                <a href="{{ route('gatepass.unreleased.voyage', ['shipNum' => $shipNum, 'voyageNum' => $voyage->voyageNum]) }}" 
                                   class="block px-3 py-2 bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800 rounded text-blue-700 dark:text-blue-200 text-sm font-medium transition-colors">
                                    Voyage {{ $voyage->voyageNum }} ( {{ strtoupper($voyage->origin) }} - {{ strtoupper($voyage->destination) }} )
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 dark:text-gray-400">No ships found in the system.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
