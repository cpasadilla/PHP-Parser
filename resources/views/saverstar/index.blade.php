<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('M/V Saver Star - Master List') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="card-header">
            <h5 class="font-semibold">M/V Saver Star - Voyage List</h5>
            <br>
        </div>
        
        @if($ship)
        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-semibold text-gray-700 dark:text-gray-200">Ship Status: </span>
                    @if(Auth::user()->hasSubpagePermission('saverstar', 'ships', 'edit'))
                    <form action="{{ route('saverstar.update', $ship->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PUT')
                        <select id="status" name="status" class="border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-md focus:ring focus:ring-indigo-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" onchange="this.form.submit()">
                            <option value="READY" {{ $ship->status == 'READY' ? 'selected' : '' }}>READY</option>
                            <option value="CREATE BL" {{ $ship->status == 'CREATE BL' ? 'selected' : '' }}>CREATE BL</option>
                            <option value="STOP BL" {{ $ship->status == 'STOP BL' ? 'selected' : '' }}>STOP BL</option>
                        </select>
                    </form>
                    @else
                    <span class="px-3 py-2 text-sm font-medium
                        @if($ship->status == 'READY') text-black
                        @elseif($ship->status == 'CREATE BL') text-blue-600
                        @elseif($ship->status == 'STOP BL') text-red-600
                        @endif">
                        {{ $ship->status }}
                    </span>
                    @endif
                </div>
                @if(Auth::user()->hasSubpagePermission('saverstar', 'bl', 'create') && $ship->status == 'CREATE BL')
                <a href="{{ route('saverstar.create-bl') }}" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    + Create New BL
                </a>
                @endif
            </div>
        </div>
        @endif
        
        <table class="w-full border-collapse">
            <thead class="bg-gray-200 dark:bg-dark-eval-0">
                <tr>
                    <th class="p-2 text-gray-700 dark:text-white">VOYAGE NUMBER</th>
                    <th class="p-2 text-gray-700 dark:text-white">TOTAL BLs</th>
                    @if(Auth::user()->hasSubpagePermission('saverstar', 'ships', 'delete'))
                    <th class="p-2 text-gray-700 dark:text-white">ACTION</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if($voyages->isNotEmpty())
                    @foreach ($voyages as $voyage)
                    <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                        <td class="p-2 text-center">
                            <a href="{{ route('saverstar.voyage.list', ['voyageNum' => $voyage->voyageNum]) }}" class="text-blue-600 dark:text-blue-400 font-medium text-lg hover:underline">
                                {{ $voyage->voyageNum }}
                            </a>
                        </td>
                        <td class="p-2 text-center">
                            <span class="text-gray-600 dark:text-gray-400">{{ $voyage->bl_count }} BL(s)</span>
                        </td>
                        @if(Auth::user()->hasSubpagePermission('saverstar', 'ships', 'delete'))
                        <td class="p-2 text-center">
                            <form action="{{ route('saverstar.voyage.delete', $voyage->voyageNum) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this voyage and all its BLs ({{ $voyage->bl_count }} BL(s))?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 text-white bg-red-500 rounded-md hover:bg-red-600">
                                    Delete
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="p-4 text-center text-gray-500">No voyages found. Create a BL to start a new voyage.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <br>

    <!-- Status Notes Section -->
    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
        <h5 class="font-bold text-gray-700 dark:text-gray-200">Note:</h5>
        <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300">
            <li><span class="font-bold text-blue-600">CREATE BL:</span> You can create a BL.</li>
            <li><span class="font-bold text-red-600">STOP BL:</span> You cannot create a BL.</li>
            <li><span class="font-bold text-gray-700 dark:text-gray-200">Delete Voyage:</span> Deleting a voyage will remove all BLs associated with that voyage number.</li>
        </ul>
    </div>
    <br>
</x-app-layout>
