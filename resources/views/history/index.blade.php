<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <button onclick="window.location.href='{{ route('history') }}';" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('History') }}
            </h2>
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 dark:text-gray-300">
        <ul class="flex border-b mb-4 dark:border-gray-700">
            <li class="-mb-px mr-1">
                <a class="tab-link bg-white inline-block border-l border-t border-r rounded-t py-2 px-4 text-blue-700 font-semibold dark:bg-gray-800 dark:text-blue-400" href="#user-activity" onclick="showTab('user-activity')">User Activity</a>
            </li>
            <li class="mr-1">
                <a class="tab-link bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold dark:bg-gray-800 dark:text-blue-300 dark:hover:text-blue-500" href="#order-update-logs" onclick="showTab('order-update-logs')">Order Update Logs</a>
            </li>
            <li class="mr-1">
                <a class="tab-link bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold dark:bg-gray-800 dark:text-blue-300 dark:hover:text-blue-500" href="#order-delete-logs" onclick="showTab('order-delete-logs')">Delete BL History</a>
            </li>
        </ul>

        <div id="user-activity" class="tab-content mt-4">
            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">User Activity</h3>
            <form method="GET" action="{{ route('history') }}" class="flex flex-wrap gap-4 mb-4">
                <input type="hidden" name="tab" value="user-activity">
                <select name="name" class="border rounded px-4 py-2 flex-grow dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Users</option>
                    @foreach ($allUsers as $user)
                        <option value="{{ $user->name }}" {{ request('name') == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <select name="per_page" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                    <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                    <option value="999999" {{ request('per_page') == '999999' ? 'selected' : '' }}>All items</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">Filter</button>
            </form>
            <div class="overflow-x-auto">
                <table class="table-auto w-full mt-4 border-collapse border border-gray-200 dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th class="px-4 py-2 border dark:border-gray-700">Name</th>
                            <th class="px-4 py-2 border dark:border-gray-700">IP Address</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userActivities as $activity)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $activity->name }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $activity->ip_address }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ date('Y-m-d H:i:s', $activity->last_activity) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $userActivities->appends(['tab' => 'user-activity', 'name' => request('name'), 'per_page' => request('per_page'), 'sort' => request('sort')])->links() }}
            </div>
        </div>

        <div id="order-update-logs" class="tab-content mt-4 hidden">
            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Order Update Logs</h3>
            <form method="GET" action="{{ route('history') }}" class="flex flex-wrap gap-4 mb-4">
                <input type="hidden" name="tab" value="order-update-logs">
                <input type="text" name="search" placeholder="Search BL #" class="border rounded px-4 py-2 flex-grow dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300" value="{{ request('search') }}">
                <select name="updated_by" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Users</option>
                    @foreach ($allUsers as $user)
                        <option value="{{ $user->name }}" {{ request('updated_by') == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <select name="ship" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Ships</option>
                    @foreach ($allShips as $ship)
                        <option value="{{ $ship }}" {{ request('ship') == $ship ? 'selected' : '' }}>{{ $ship }}</option>
                    @endforeach
                </select>
                <select name="voyage" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Voyages</option>
                    @foreach ($allVoyages as $voyage)
                        <option value="{{ $voyage }}" {{ request('voyage') == $voyage ? 'selected' : '' }}>{{ $voyage }}</option>
                    @endforeach
                </select>
                <select name="field_name" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Fields</option>
                    <option value="OR" {{ request('field_name') == 'OR' ? 'selected' : '' }}>OR Number</option>
                    <option value="AR" {{ request('field_name') == 'AR' ? 'selected' : '' }}>AR Number</option>
                    <option value="freight" {{ request('field_name') == 'freight' ? 'selected' : '' }}>Freight</option>
                    <option value="value" {{ request('field_name') == 'value' ? 'selected' : '' }}>Value</option>
                    <option value="valuation" {{ request('field_name') == 'valuation' ? 'selected' : '' }}>Valuation</option>
                    <option value="wharfage" {{ request('field_name') == 'wharfage' ? 'selected' : '' }}>Wharfage</option>
                    <option value="other" {{ request('field_name') == 'other' ? 'selected' : '' }}>Other</option>
                    <option value="discount" {{ request('field_name') == 'discount' ? 'selected' : '' }}>Discount</option>
                    <option value="bir" {{ request('field_name') == 'bir' ? 'selected' : '' }}>BIR</option>
                    <option value="totalAmount" {{ request('field_name') == 'totalAmount' ? 'selected' : '' }}>Total Amount</option>
                    <option value="containerNum" {{ request('field_name') == 'containerNum' ? 'selected' : '' }}>Container Number</option>
                    <option value="remark" {{ request('field_name') == 'remark' ? 'selected' : '' }}>Remark</option>
                    <option value="note" {{ request('field_name') == 'note' ? 'selected' : '' }}>Note</option>
                    <option value="checkName" {{ request('field_name') == 'checkName' ? 'selected' : '' }}>Checker Name</option>
                    <option value="cargoType" {{ request('field_name') == 'cargoType' ? 'selected' : '' }}>Cargo Type</option>
                    <option value="blStatus" {{ request('field_name') == 'blStatus' ? 'selected' : '' }}>BL Status</option>
                    <option value="image" {{ request('field_name') == 'image' ? 'selected' : '' }}>Image</option>
                </select>
                <select name="action_type" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Actions</option>
                    <option value="update" {{ request('action_type') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="create" {{ request('action_type') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="delete" {{ request('action_type') == 'delete' ? 'selected' : '' }}>Delete</option>
                </select>
                <select name="per_page" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                    <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                    <option value="999999" {{ request('per_page') == '999999' ? 'selected' : '' }}>All items</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">Filter</button>
            </form>
            <div class="overflow-x-auto">
                <table class="table-auto w-full mt-4 border-collapse border border-gray-200 dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th class="px-4 py-2 border dark:border-gray-700" hidden>Order ID</th>
                            <th class="px-4 py-2 border dark:border-gray-700">
                                <a href="{{ route('history', array_merge(request()->query(), ['tab' => 'order-update-logs', 'sort' => request('sort') === 'bl_asc' ? 'bl_desc' : 'bl_asc'])) }}" class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">
                                    BL # 
                                    @if(request('sort') === 'bl_asc')
                                        ↑
                                    @elseif(request('sort') === 'bl_desc')
                                        ↓
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-2 border dark:border-gray-700">Ship</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Voyage</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Field Updated</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Old Value</th>
                            <th class="px-4 py-2 border dark:border-gray-700">New Value</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Action</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Updated By</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderUpdateLogs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border px-4 py-2 dark:border-gray-700" hidden>{{ $log->order_id }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->bl_number }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->ship_name }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->voyage_number }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">
                                    @if($log->field_name)
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst(str_replace('_', ' ', $log->field_name)) }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">General Update</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center max-w-xs">
                                    @if($log->old_value !== null)
                                        <div class="truncate" title="{{ $log->old_value }}">
                                            @if(strlen($log->old_value) > 50)
                                                {{ substr($log->old_value, 0, 50) }}...
                                            @else
                                                {{ $log->old_value }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 italic">empty</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center max-w-xs">
                                    @if($log->new_value !== null)
                                        <div class="truncate" title="{{ $log->new_value }}">
                                            @if(strlen($log->new_value) > 50)
                                                {{ substr($log->new_value, 0, 50) }}...
                                            @else
                                                {{ $log->new_value }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 italic">empty</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">
                                    @if($log->action_type === 'update')
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Update</span>
                                    @elseif($log->action_type === 'create')
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Create</span>
                                    @elseif($log->action_type === 'delete')
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Delete</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">{{ ucfirst($log->action_type) }}</span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->updated_by }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->updated_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orderUpdateLogs->appends(['tab' => 'order-update-logs', 'updated_by' => request('updated_by'), 'field_name' => request('field_name'), 'action_type' => request('action_type'), 'search' => request('search'), 'ship' => request('ship'), 'voyage' => request('voyage'), 'per_page' => request('per_page'), 'sort' => request('sort')])->links() }}
            </div>
        </div>

        <div id="order-delete-logs" class="tab-content mt-4 hidden">
            <h3 class="text-lg font-semibold mb-4 dark:text-gray-200">Delete BL History</h3>
            <form method="GET" action="{{ route('history') }}" class="flex flex-wrap gap-4 mb-4">
                <input type="hidden" name="tab" value="order-delete-logs">
                <select name="deleted_by" class="border rounded px-4 py-2 flex-grow dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Users</option>
                    @foreach ($allUsers as $user)
                        <option value="{{ $user->name }}" {{ request('deleted_by') == $user->name ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                <select name="restore_status" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="">All Status</option>
                    <option value="deleted" {{ request('restore_status') == 'deleted' ? 'selected' : '' }}>Deleted Only</option>
                    <option value="restored" {{ request('restore_status') == 'restored' ? 'selected' : '' }}>Restored Only</option>
                </select>
                <select name="per_page" class="border rounded px-4 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                    <option value="15" {{ request('per_page') == '15' ? 'selected' : '' }}>15 per page</option>
                    <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20 per page</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 per page</option>
                    <option value="999999" {{ request('per_page') == '999999' ? 'selected' : '' }}>All items</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700">Filter</button>
            </form>
            <div class="overflow-x-auto">
                <table class="table-auto w-full mt-4 border-collapse border border-gray-200 dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th class="px-4 py-2 border dark:border-gray-700">
                                <a href="{{ route('history', array_merge(request()->query(), ['tab' => 'order-delete-logs', 'sort' => request('sort') === 'bl_asc' ? 'bl_desc' : 'bl_asc'])) }}" class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400">
                                    BL # 
                                    @if(request('sort') === 'bl_asc')
                                        ↑
                                    @elseif(request('sort') === 'bl_desc')
                                        ↓
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-2 border dark:border-gray-700">Ship</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Voyage</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Shipper</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Consignee</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Total Amount</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Deleted By</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Deleted At</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Status</th>
                            <th class="px-4 py-2 border dark:border-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orderDeleteLogs as $log)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->bl_number }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->ship_name }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->voyage_number }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->shipper_name }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->consignee_name }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">₱{{ number_format($log->total_amount ?? 0, 2) }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->deleted_by }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">
                                    @if($log->restored_at)
                                        <span class="text-green-600 dark:text-green-400 font-semibold">
                                            ✓ Restored
                                        </span>
                                        <br>
                                        <small class="text-gray-500 dark:text-gray-400">
                                            {{ $log->restored_at->format('Y-m-d H:i:s') }}
                                            <br>by {{ $log->restored_by }}
                                        </small>
                                    @else
                                        <span class="text-red-600 dark:text-red-400 font-semibold">
                                            ✗ Deleted
                                        </span>
                                    @endif
                                </td>
                                <td class="border px-4 py-2 dark:border-gray-700 text-center">
                                    @if(!$log->restored_at)
                                        <form action="{{ route('masterlist.restore-order', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to restore this order? This will create a new BL with the same data.');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 dark:bg-green-600 dark:hover:bg-green-700">
                                                🔄 Restore
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('masterlist.view-bl', ['shipNum' => $log->restoredOrder->shipNum, 'voyageNum' => $log->restoredOrder->voyageNum, 'orderId' => $log->restored_order_id]) }}" class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 inline-block">
                                            👁️ View Restored
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="border px-4 py-8 dark:border-gray-700 text-center text-gray-500 dark:text-gray-400">
                                    No deleted orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orderDeleteLogs->appends(['tab' => 'order-delete-logs', 'deleted_by' => request('deleted_by'), 'restore_status' => request('restore_status'), 'per_page' => request('per_page'), 'sort' => request('sort')])->links() }}
            </div>
        </div>
    </div>

    <style>
        .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .max-w-xs {
            max-width: 20rem;
        }
        
        /* Better contrast for badges */
        .bg-blue-100 { background-color: #dbeafe; }
        .text-blue-800 { color: #1e40af; }
        .bg-green-100 { background-color: #dcfce7; }
        .text-green-800 { color: #166534; }
        .bg-red-100 { background-color: #fee2e2; }
        .text-red-800 { color: #991b1b; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .text-gray-800 { color: #1f2937; }
        
        /* Dark mode badge colors */
        .dark .bg-blue-900 { background-color: #1e3a8a; }
        .dark .text-blue-200 { color: #bfdbfe; }
        .dark .bg-green-900 { background-color: #14532d; }
        .dark .text-green-200 { color: #bbf7d0; }
        .dark .bg-red-900 { background-color: #7f1d1d; }
        .dark .text-red-200 { color: #fecaca; }
        .dark .bg-gray-900 { background-color: #111827; }
        .dark .text-gray-200 { color: #e5e7eb; }
        
        /* Improved table responsiveness */
        .table-auto {
            table-layout: auto;
        }
        
        .overflow-x-auto {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Better form styling */
        .flex-wrap.gap-4 > * {
            min-width: 150px;
        }
        
        .flex-wrap.gap-4 > select:first-of-type {
            flex-grow: 1;
            min-width: 200px;
        }
        
        /* Sort indicators */
        .sort-arrow {
            display: inline-block;
            margin-left: 5px;
            font-size: 12px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'user-activity';
            showTab(activeTab);
        });

        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            document.getElementById(tabId).classList.remove('hidden');

            document.querySelectorAll('.tab-link').forEach(link => {
                link.classList.remove('text-blue-700', 'border-l', 'border-t', 'border-r', 'rounded-t');
                link.classList.add('text-blue-500', 'hover:text-blue-800', 'dark:text-blue-300', 'dark:hover:text-blue-500');
            });
            document.querySelector(`[href="#${tabId}"]`).classList.add('text-blue-700', 'border-l', 'border-t', 'border-r', 'rounded-t', 'dark:text-blue-400');
        }
    </script>
</x-app-layout>