<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Master List') }}
            </h2>
        </div>
    </x-slot>

    <div class="flex items-center justify-between w-full">
        <!-- CREATE CUSTOMER BUTTON -->
        @if(Auth::user()->hasSubpagePermission('masterlist', 'ships', 'create'))
        <div class="ml-auto" hidden>
            <!-- Alpine.js Modal Component -->
            <div x-data="{ openModal: false }">
                <!-- Open Modal Button -->
                <button @click="openModal = true" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    + Add Ship
                </button>

                <!-- Modal -->
                <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-1/3">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-4">+ Add Ship</h2>

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="list-disc list-inside text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form -->
                        <form method="POST" action="{{ route('masterlist.store') }}">
                            @csrf
                            <label class="text-gray-700 dark:text-gray-300">Ship Number</label>
                            <input type="text" name="ship_number" class="w-full p-2 border rounded-md text-gray-800 dark:text-white bg-gray-100 dark:bg-gray-700 focus:ring focus:ring-indigo-300">

                            <!-- Submit & Close Buttons -->
                            <div class="flex justify-end mt-4">
                                <button type="button" @click="openModal = false" class="px-4 py-2 bg-gray-500 text-white rounded">
                                    Cancel
                                </button>
                                <button type="submit" class="ml-2 px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <br>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="card-header">
            <h5 class="font-semibold">Ship Lists</h5>
            <br>
        </div>
        <table class="w-full border-collapse">
            <thead class="bg-gray-200 dark:bg-dark-eval-0">
                <tr>
                    <th class="p-2 text-gray-700 dark:text-white">SHIP #</th>
                    <th class="p-2 text-gray-700 dark:text-white">STATUS</th>
                    @if(Auth::user()->hasSubpagePermission('masterlist', 'ships', 'delete'))
                    <th class="p-2 text-gray-700 dark:text-white">ACTION</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($ships as $ship)
                    <tr class="border-b hover:bg-gray-100 dark:hover:bg-gray-700">
                        <td class="p-2 text-center">
                            <a href="{{ route('masterlist.voyage', ['id' => $ship->id]) }}" class="text-blue-600 dark:text-blue-400 font-medium hover:underline">
                            M/V Everwin Star {{ $ship->ship_number }}
                            </a>
                        </td>
                        <td class="p-2 text-center">
                            @if(Auth::user()->hasSubpagePermission('masterlist', 'ships', 'edit'))
                            <form action="{{ route('masterlist.update', $ship->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select id= 'status' name="status" class="border border-gray-300 dark:border-gray-600 px-3 py-2 rounded-md focus:ring focus:ring-indigo-300 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" onchange="this.form.submit()">
                                    <option value="READY" {{ $ship->status == 'READY' ? 'selected' : '' }}>READY</option>
                                    <option value="CREATE BL" {{ $ship->status == 'CREATE BL' ? 'selected' : '' }}>CREATE BL</option>
                                    <option value="STOP BL" {{ $ship->status == 'STOP BL' ? 'selected' : '' }}>STOP BL</option>
                                    <option value="NEW VOYAGE" {{ $ship->status == 'NEW VOYAGE' ? 'selected' : '' }}>NEW VOYAGE</option>
                                    <option value="DRY DOCKED" {{ $ship->status == 'DRY DOCKED' ? 'selected' : '' }}>DRY DOCKED</option>
                                </select>
                            </form>
                            @else
                            <span class="px-3 py-2 text-sm font-medium
                                @if($ship->status == 'READY') text-black
                                @elseif($ship->status == 'CREATE BL') text-blue-600
                                @elseif($ship->status == 'STOP BL') text-red-600
                                @elseif($ship->status == 'NEW VOYAGE') text-green-600
                                @elseif($ship->status == 'DRY DOCKED') text-yellow-500
                                @endif">
                                {{ $ship->status }}
                            </span>
                            @endif
                        </td>
                        @if(Auth::user()->hasSubpagePermission('masterlist', 'ships', 'delete'))
                            <td class="p-2 text-center flex justify-center gap-2">
                                <form action="{{ route('masterlist.destroy', $ship->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ship?');">
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
            </tbody>
        </table>
    </div>
    <br>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <h5 class="font-bold text-gray-700 dark:text-gray-200">Note:</h5>
        <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300">
            <li><span class="font-bold text-blue-600">CREATE BL:</span> You can create a BL.</li>
            <li><span class="font-bold text-red-600">STOP BL:</span> You cannot create a BL.</li>
            <li><span class="font-bold text-green-600">NEW VOYAGE:</span> A new voyage number will be generated. </li>
            <li><span class="font-bold" style="color: #facc15;">DRY DOCKED:</span> No BL creation.</li>
        </ul>
    </div>
    <br>
</x-app-layout>
<style>
    .option-ready {
        color: black;
    }
    .option-create-bl {
        color: #2563eb; /* Blue */
    }
    .option-stop-bl {
        color: #dc2626; /* Red */
    }
    .option-new-voyage {
        color: #16a34a; /* Green */
    }
    .option-drydocked {
        color: #facc15; /* Yellow */
    }
</style>
