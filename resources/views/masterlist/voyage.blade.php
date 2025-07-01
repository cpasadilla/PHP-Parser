<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('M/V Everwin Star ') . $ship->ship_number}}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
    <div class="card-header">
        <h5 class="font-semibold">Voyage List</h5>
        <br>
    </div>

    <table class="w-full border-collapse">
        <thead class="bg-gray-200 dark:bg-dark-eval-0">
            <tr>
                <th class="p-2 text-gray-700 dark:text-white">Voyage Number</th>
                <th class="p-2 text-gray-700 dark:text-white" hidden>Ship</th>
                <th class="p-2 text-gray-700 dark:text-white">Origin</th>
                <th class="p-2 text-gray-700 dark:text-white">Destination</th>
                <th class="p-2 text-gray-700 dark:text-white">Status</th>
                <th class="p-2 text-gray-700 dark:text-white">Last Updated</th>
                <th class="p-2 text-gray-700 dark:text-white">Total of BL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($voyages as $voyage)
            @php
                if ($ship->ship_number == 'I' || $ship->ship_number == 'II') {
                    $key = $voyage->v_num . '-' . $voyage->inOut;
                }
                else{
                    $key = $voyage->v_num;
                }
                $orderCount = $orderCounts[$key] ?? 0; // Get the count or default to 0
            @endphp

            <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                <td class="p-2 text-center">
                    <a href="{{ route('masterlist.voyage-orders', ['shipNum' => $voyage->ship, 'voyageNum' => $key]) }}" class="text-blue-500 hover:underline">
                        {{ $key }}
                    </a>
                </td>
                <td class="p-2 text-center" hidden>{{ $voyage->ship }}</td>
                <td class="p-2 text-center">{{ $voyageRoutes[$key]['origin'] }}</td>
                <td class="p-2 text-center">{{ $voyageRoutes[$key]['destination'] }}</td>
                <td class="p-2 text-center">
                    @if(Auth::user()->hasSubpagePermission('masterlist', 'voyage', 'edit'))
                    <form action="{{ route('voyage.update-status', ['id' => $voyage->id]) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <select name="status" onchange="this.form.submit()" class="px-4 py-2 pr-8 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[100px] appearance-none bg-no-repeat bg-right @if($voyage->lastStatus == 'READY') bg-green-100 text-green-800 @elseif($voyage->lastStatus == 'STOP') bg-red-100 text-red-800 @else bg-gray-100 @endif" style="background-image: url('data:image/svg+xml;charset=US-ASCII,<svg width=\"12\" height=\"12\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M4 8 0 4h8z\" fill=\"%23888\"/></svg>'); background-position: right 0.5rem center; background-size: 12px;">
                            <option value="READY" {{ $voyage->lastStatus == 'READY' ? 'selected' : '' }}>READY</option>
                            <option value="STOP" {{ $voyage->lastStatus == 'STOP' ? 'selected' : '' }}>STOP</option>
                        </select>
                    </form>
                    @else
                    <span class="px-4 py-2 text-sm @if($voyage->lastStatus == 'READY') text-green-800 @elseif($voyage->lastStatus == 'STOP') text-red-800 @else text-gray-600 @endif">
                        {{ $voyage->lastStatus }}
                    </span>
                    @endif
                </td>
                <td class="p-2 text-center">{{ \Carbon\Carbon::parse($voyage->lastUpdated)->format('F d, Y') }}</td>
                <td class="p-2 text-center">{{ $orderCount }}</td> <!-- Display count directly -->
            </tr>
        @endforeach

        </tbody>
    </table>
</div>
    <br>
</x-app-layout>
