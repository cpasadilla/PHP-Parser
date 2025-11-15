<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight flex items-center gap-2">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Gate Pass List') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <!-- Filter Section -->
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Search & Filter</h3>
            <form method="GET" action="{{ route('gatepass.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Gate Pass No.</label>
                    <input type="text" name="gate_pass_no" value="{{ request('gate_pass_no') }}" 
                           class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                           placeholder="Search gate pass...">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">BL Number</label>
                    <input type="text" name="bl_number" value="{{ request('bl_number') }}" 
                           class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                           placeholder="Search BL...">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Container Number</label>
                    <input type="text" name="container" value="{{ request('container') }}" 
                           class="w-full px-3 py-2 border rounded-md dark:bg-gray-700 dark:border-gray-600" 
                           placeholder="Search container...">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 mr-2">
                        Search
                    </button>
                    <a href="{{ route('gatepass.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Gate Pass Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-200 dark:bg-dark-eval-0">
                    <tr>
                        <th class="p-3 border border-gray-300">Gate Pass No.</th>
                        <th class="p-3 border border-gray-300">Ship #</th>
                        <th class="p-3 border border-gray-300">Voyage #</th>
                        <th class="p-3 border border-gray-300">BL #</th>
                        <th class="p-3 border border-gray-300">Container #</th>
                        <th class="p-3 border border-gray-300">Shipper</th>
                        <th class="p-3 border border-gray-300">Consignee</th>
                        <th class="p-3 border border-gray-300">Release Date</th>
                        <th class="p-3 border border-gray-300">Checker</th>
                        <th class="p-3 border border-gray-300">Receiver</th>
                        <th class="p-3 border border-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gatePasses as $gatePass)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="p-3 border border-gray-300 text-center font-semibold">
                                {{ $gatePass->gate_pass_no }}
                            </td>
                            <td class="p-3 border border-gray-300 text-center">
                                {{ $gatePass->order->shipNum }}
                            </td>
                            <td class="p-3 border border-gray-300 text-center">
                                {{ $gatePass->order->voyageNum }}
                            </td>
                            <td class="p-3 border border-gray-300 text-center">
                                <a href="{{ route('masterlist.view-bl', ['shipNum' => $gatePass->order->shipNum, 'voyageNum' => $gatePass->order->voyageNum, 'orderId' => $gatePass->order->id]) }}" 
                                   class="text-blue-600 hover:underline" 
                                   target="_blank">
                                    {{ $gatePass->order->orderId }}
                                </a>
                            </td>
                            <td class="p-3 border border-gray-300 text-center">
                                {{ $gatePass->container_number }}
                            </td>
                            <td class="p-3 border border-gray-300">
                                {{ $gatePass->shipper_name }}
                            </td>
                            <td class="p-3 border border-gray-300">
                                {{ $gatePass->consignee_name }}
                            </td>
                            <td class="p-3 border border-gray-300 text-center">
                                {{ \Carbon\Carbon::parse($gatePass->release_date)->format('M d, Y') }}
                            </td>
                            <td class="p-3 border border-gray-300">
                                {{ $gatePass->checker_name }}
                            </td>
                            <td class="p-3 border border-gray-300">
                                {{ $gatePass->receiver_name }}
                            </td>
                            <td class="p-3 border border-gray-300 text-center">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('gatepass.show', $gatePass->id) }}" 
                                       class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
                                       target="_blank">
                                        View
                                    </a>
                                    <a href="{{ route('gatepass.edit', $gatePass->id) }}" 
                                       class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm">
                                        Edit
                                    </a>
                                    <a href="{{ route('gatepass.summary', ['order_id' => $gatePass->order_id]) }}" 
                                       class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm"
                                       target="_blank">
                                        Summary
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-6 text-center text-gray-500">
                                No gate passes found. 
                                @if(!request()->hasAny(['gate_pass_no', 'bl_number', 'container']))
                                    Gate passes will appear here once created from the masterlist.
                                @else
                                    Try adjusting your search filters.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $gatePasses->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>
