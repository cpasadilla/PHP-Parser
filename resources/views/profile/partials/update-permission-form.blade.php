<section>
    <header>
        <h2 class="text-lg font-medium">
            {{ __('User Page Permission') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update user permission per page and operation") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    
    <div class="mt-4">
        @if (session('status') === 'permissions-updated')
            <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                {{ __('User permissions have been updated.') }}
            </div>
        @endif
        
        @if (session('status') === 'location-added')
            <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                {{ session('message', 'New location has been added.') }}
            </div>
        @endif
        
        @if (session('status') === 'checker-added')
            <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                {{ session('message', 'New checker has been added.') }}
            </div>
        @endif
    </div>

    @php
        // Fetch all users
        $users = App\Models\User::with(['roles', 'permissions'])->get();
        
        // Define application pages/modules with operations
        $modules = [
            'profile' => [
                'name' => 'Profile Settings',
                'operations' => ['access', 'edit']
            ],
            'dashboard' => [
                'name' => 'Dashboard',
                'operations' => ['access'],
                'pages' => [
                    'ship-graphs' => [
                        'name' => 'Ship Graphs Section',
                        'operations' => ['access']
                    ],
                    'pie-charts' => [
                        'name' => 'Pie Chart Section',
                        'operations' => ['access']
                    ]
                ]
            ],
            'customer' => [
                'name' => 'Customer Management',
                'operations' => ['access', 'create', 'edit', 'delete']
            ],
            'users' => [
                'name' => 'Staff Management',
                'operations' => ['access', 'create', 'edit', 'delete']
            ],
            'history' => [
                'name' => 'History',
                'operations' => ['access']
            ],
            'pricelist' => [
                'name' => 'Price List',
                'operations' => ['access', 'create', 'edit', 'delete']
            ],
            'inventory' => [
                'name' => 'Inventory',
                'operations' => ['access', 'create', 'edit', 'delete']
            ],
            'masterlist' => [
                'name' => 'Master List',
                'operations' => ['access'],
                'pages' => [
                    'ships' => [
                        'name' => 'Ship Management',
                        'operations' => ['access', 'edit', 'delete']
                    ],
                    'voyage' => [
                        'name' => 'Voyage Management',
                        'operations' => ['access', 'edit']
                    ],
                    'list' => [
                        'name' => 'Order List',
                        'operations' => ['access', 'edit', 'delete']
                    ],
                    'customer' => [
                        'name' => 'Customer List',
                        'operations' => ['access', 'edit', 'delete']
                    ],
                    'container' => [
                        'name' => 'Container Management',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ],
                    'container-details' => [
                        'name' => 'Container Details',
                        'operations' => ['access']
                    ],
                    'parcel' => [
                        'name' => 'Parcel Management',
                        'operations' => ['access']
                    ],
                    'soa' => [
                        'name' => 'Statement of Account',
                        'operations' => ['access', 'create', 'delete']
                    ]
                ]
            ],
            'crew' => [
                'name' => 'Crew Management',
                'operations' => ['access', 'create', 'edit', 'delete'],
                'pages' => [
                    'crew-management' => [
                        'name' => 'Crew Management',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ],
                    'document-management' => [
                        'name' => 'Document Management',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ],
                    'expiring-documents' => [
                        'name' => 'Expiring Documents',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ],
                    'leave-applications' => [
                        'name' => 'Leave Applications',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ],
                    'upload-sick-leave' => [
                        'name' => 'Upload Sick Leave Form',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ],
                    'leave-credits' => [
                        'name' => 'Leave Credits Management',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ]
                ]
            ],
            'saverstar' => [
                'name' => 'M/V Saver Star',
                'operations' => ['access', 'create', 'edit', 'delete'],
                'pages' => [
                    'ships' => [
                        'name' => 'Ship Status Management',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ],
                    'bl' => [
                        'name' => 'Bill of Lading Management',
                        'operations' => ['access', 'create', 'edit', 'delete']
                    ]
                ]
            ]
        ];
        
        // Current user
        $currentUser = auth()->user();
        $userRole = $currentUser->roles ? strtoupper(trim($currentUser->roles->roles)) : '';
        $isAdmin = in_array($userRole, ['ADMIN', 'ADMINISTRATOR']);
    @endphp

    <div class="mb-4 text-sm text-gray-700 dark:text-gray-300">
       Role: <strong>{{ $currentUser->roles ? $currentUser->roles->roles : 'No role assigned' }}</strong>
    </div>

    <form
        method="post"
        action="{{ route('user-permissions.update') }}"
        class="mt-6"
        id="permissions-form"
    >
        @csrf

        @if($isAdmin)
            <!-- User Tabs -->
            <div x-data="{ activeTab: 0 }" class="mt-4">
                <!-- Tab Navigation - Grid layout that wraps to multiple rows -->
                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-wrap gap-1 pb-1">
                        @foreach($users as $index => $user)
                            @php
                                $userAdminRole = $user->roles && in_array(strtoupper(trim($user->roles->roles)), ['ADMIN', 'ADMINISTRATOR']);
                                $displayName = $user->fName . ' ' . $user->lName;
                            @endphp
                            <button 
                                type="button"
                                @click="activeTab = {{ $index }}"
                                :class="{ 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200': activeTab === {{ $index }}, 'bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700': activeTab !== {{ $index }} }"
                                class="py-2 px-3 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 focus:outline-none transition mb-2"
                                title="{{ $user->fName }} {{ $user->lName }} ({{ $user->roles ? $user->roles->roles : 'No Role' }})"
                            >
                                <div class="flex items-center">
                                    <span>{{ $displayName }}</span>
                                    @if($userAdminRole)
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                            A
                                        </span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
                
                <!-- Tab Content -->
                @foreach($users as $index => $user)
                    @php
                        $userAdminRole = $user->roles && in_array(strtoupper(trim($user->roles->roles)), ['ADMIN', 'ADMINISTRATOR']);
                        
                        // Get user permissions from database
                        $userPermissions = [];
                        if ($user->permissions && $user->permissions->pages) {
                            $userPermissions = is_string($user->permissions->pages) ? json_decode($user->permissions->pages, true) : $user->permissions->pages;
                        }
                        
                        // Debug: Add debugging info
                        $debugPermissions = false; // Set to true to enable debugging
                    @endphp
                    
                    <div x-show="activeTab === {{ $index }}" x-cloak class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4">
                        @if($debugPermissions)
                        <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded">
                            <h4 class="font-bold">Debug - User Permissions:</h4>
                            <pre class="text-xs overflow-auto">{{ json_encode($userPermissions, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center justify-between mb-4">
                            <div class="mb-2 sm:mb-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $user->fName }} {{ $user->lName }}
                                </h3>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Email: {{ $user->email }} | Role: {{ $user->roles ? $user->roles->roles : 'No Role' }}
                                </div>
                            </div>
                            
                            @if(!$userAdminRole)
                                <div class="flex flex-wrap gap-2">
                                    <button 
                                        type="button"
                                        onclick="selectAllPermissions({{ $index }})"
                                        class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150"
                                    >
                                        Select All
                                    </button>
                                    <button 
                                        type="button"
                                        onclick="deselectAllPermissions({{ $index }})"
                                        class="inline-flex items-center px-3 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:shadow-outline-yellow transition ease-in-out duration-150"
                                    >
                                        Deselect All
                                    </button>
                                    <button 
                                        type="button"
                                        onclick="deletePermissions('{{ $user->id }}')"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150"
                                    >
                                        Delete All Permissions
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Module
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Access
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Create
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Edit
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Delete
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                                    @foreach($modules as $moduleKey => $module)
                                        <tr class="{{ $loop->odd ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $module['name'] }}
                                            </td>
                                            
                                            @foreach(['access', 'create', 'edit', 'delete'] as $operation)
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @if(in_array($operation, $module['operations']))
                                                        @if($userAdminRole)
                                                            <input 
                                                                type="checkbox" 
                                                                name="permissions[{{ $user->id }}][{{ $moduleKey }}][{{ $operation }}]" 
                                                                value="1"
                                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                                                                checked disabled
                                                            >
                                                            <input type="hidden" name="permissions[{{ $user->id }}][{{ $moduleKey }}][{{ $operation }}]" value="1">
                                                        @else
                                                            <input 
                                                                type="checkbox" 
                                                                name="permissions[{{ $user->id }}][{{ $moduleKey }}][{{ $operation }}]" 
                                                                value="1"
                                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                                                                {{ isset($userPermissions[$moduleKey][$operation]) && $userPermissions[$moduleKey][$operation] ? 'checked' : '' }}
                                                            >
                                                        @endif
                                                    @else
                                                        <span class="text-gray-400 dark:text-gray-500">—</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        
                                        {{-- Display subpages if available --}}
                                        @if(isset($module['pages']))
                                            @foreach($module['pages'] as $pageKey => $page)
                                                <tr class="bg-gray-100 dark:bg-gray-850">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 pl-10">
                                                        ↳ {{ $page['name'] }}
                                                    </td>
                                                    
                                                    @foreach(['access', 'create', 'edit', 'delete'] as $operation)
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            @if(in_array($operation, $page['operations']))
                                                                @if($userAdminRole)
                                                                    <input 
                                                                        type="checkbox" 
                                                                        name="permissions[{{ $user->id }}][{{ $moduleKey }}][pages][{{ $pageKey }}][{{ $operation }}]" 
                                                                        value="1"
                                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                                                                        checked disabled
                                                                    >
                                                                    <input type="hidden" name="permissions[{{ $user->id }}][{{ $moduleKey }}][pages][{{ $pageKey }}][{{ $operation }}]" value="1">
                                                                @else
                                                                    <input 
                                                                        type="checkbox" 
                                                                        name="permissions[{{ $user->id }}][{{ $moduleKey }}][pages][{{ $pageKey }}][{{ $operation }}]" 
                                                                        value="1"
                                                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                                                                        {{ isset($userPermissions[$moduleKey]['pages'][$pageKey][$operation]) && $userPermissions[$moduleKey]['pages'][$pageKey][$operation] ? 'checked' : '' }}
                                                                    >
                                                                @endif
                                                            @else
                                                                <span class="text-gray-400 dark:text-gray-500">—</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center gap-4 mt-4">
                <x-button variant="primary" type="submit">
                    {{ __('Save Permissions') }}
                </x-button>
                
                <x-button variant="secondary" type="button" id="add-location-btn">
                    {{ __('Add New Location') }}
                </x-button>
                
                <x-button variant="secondary" type="button" id="add-checker-btn">
                    {{ __('Add New Checker') }}
                </x-button>
            </div>
            
            @if (session('status') === 'permissions-updated')
                <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                    {{ __('User permissions have been updated.') }}
                </div>
            @endif
            
            @if (session('status') === 'location-added')
                <div class="mt-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                    {{ __('New location has been added successfully.') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif
        @else
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg p-4 dark:bg-yellow-900/30 dark:border-yellow-800 dark:text-yellow-200">
                <p>You need administrator privileges to manage user permissions. Your current role is <strong>{{ $currentUser->roles ? $currentUser->roles->roles : 'No role assigned' }}</strong>.</p>
            </div>
        @endif
    </form>

    <!-- Delete permission confirmation modal -->
    <div id="delete-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Confirm Delete</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Are you sure you want to delete all permissions for this user? They will only be able to access their profile page.
            </p>
            <div class="mt-4 flex justify-end">
                <button 
                    type="button" 
                    onclick="closeDeleteModal()"
                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2"
                >
                    Cancel
                </button>
                <form id="delete-form" method="post" action="{{ route('user-permissions.delete') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="user_id_to_delete" name="user_id">
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:border-red-700 focus:shadow-outline-red transition ease-in-out duration-150"
                    >
                        Delete All
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Location Modal -->
    <div id="add-location-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add New Location</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Enter the name of the new location to be added to the database.
            </p>
            <form id="add-location-form" method="post" action="{{ route('locations.store') }}" class="mt-4" onsubmit="return validateLocationForm()">
                @csrf
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location Name</label>
                    <input 
                        type="text" 
                        id="location" 
                        name="location" 
                        required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter location name"
                        oninput="validateLocationName(this)"
                    >
                    <p id="location-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                <div class="mt-4 flex justify-end">
                    <button 
                        type="button" 
                        onclick="closeLocationModal()"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150"
                    >
                        Add Location
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Checker Modal -->
    <div id="add-checker-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add New Checker</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Enter the name of the new checker and select their location.
            </p>
            <form id="add-checker-form" method="post" action="{{ route('checkers.store') }}" class="mt-4" onsubmit="return validateCheckerForm()">
                @csrf
                <div>
                    <label for="checker-location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                    <select 
                        id="checker-location" 
                        name="location" 
                        required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">Select a location</option>
                        @php
                            // Get locations from the database
                            $locations = DB::table('locations')->orderBy('location')->pluck('location');
                        @endphp
                        
                        @foreach($locations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-4">
                    <label for="checker-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Checker Name</label>
                    <input 
                        type="text" 
                        id="checker-name" 
                        name="name" 
                        required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Enter checker name"
                        oninput="validateCheckerName(this)"
                    >
                    <p id="checker-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                <div class="mt-4 flex justify-end">
                    <button 
                        type="button" 
                        onclick="closeCheckerModal()"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:shadow-outline-gray transition ease-in-out duration-150 mr-2"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150"
                    >
                        Add Checker
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
    [x-cloak] { display: none !important; }
    </style>

    <script>
        function selectAllPermissions(userIndex) {
            // Get all checkboxes in the current user's tab that are not disabled
            const userTab = document.querySelector(`[x-show="activeTab === ${userIndex}"]`);
            const checkboxes = userTab.querySelectorAll('input[type="checkbox"]:not([disabled])');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            
            // Show feedback
            showPermissionFeedback('All permissions have been selected', 'success');
        }

        function deselectAllPermissions(userIndex) {
            // Get all checkboxes in the current user's tab that are not disabled
            const userTab = document.querySelector(`[x-show="activeTab === ${userIndex}"]`);
            const checkboxes = userTab.querySelectorAll('input[type="checkbox"]:not([disabled])');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Show feedback
            showPermissionFeedback('All permissions have been deselected', 'warning');
        }

        function showPermissionFeedback(message, type) {
            // Remove any existing feedback
            const existingFeedback = document.querySelector('.permission-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            // Create feedback element
            const feedback = document.createElement('div');
            feedback.className = `permission-feedback mt-2 p-2 rounded text-sm ${type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200'}`;
            feedback.textContent = message;
            
            // Insert after the buttons
            const activeTab = document.querySelector('[x-show="activeTab === ' + document.querySelector('[x-data]').__x.$data.activeTab + '"]');
            const buttonContainer = activeTab.querySelector('.flex.flex-col.sm\\:flex-row');
            buttonContainer.parentNode.insertBefore(feedback, buttonContainer.nextSibling);
            
            // Remove feedback after 3 seconds
            setTimeout(() => {
                if (feedback.parentNode) {
                    feedback.remove();
                }
            }, 3000);
        }

        function deletePermissions(userId) {
            document.getElementById('user_id_to_delete').value = userId;
            document.getElementById('delete-modal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('delete-modal').classList.add('hidden');
        }

        // Close the modal if clicking outside of it
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('delete-modal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        });

        // Location name validation function
        function validateLocationName(input) {
            const value = input.value;
            const errorElement = document.getElementById('location-error');
            const submitButton = document.querySelector('#add-location-form button[type="submit"]');
            
            // Reset error state
            errorElement.classList.add('hidden');
            errorElement.textContent = '';
            input.classList.remove('border-red-500');
            submitButton.disabled = false;
            
            // Check for symbols (anything that's not a letter, number, or space)
            if (/[^a-zA-Z0-9\s]/.test(value)) {
                errorElement.textContent = 'Symbols are not allowed in location name';
                errorElement.classList.remove('hidden');
                input.classList.add('border-red-500');
                submitButton.disabled = true;
                return false;
            }
            
            // Check for numbers
            if (/[0-9]/.test(value)) {
                errorElement.textContent = 'Numbers are not allowed in location name';
                errorElement.classList.remove('hidden');
                input.classList.add('border-red-500');
                submitButton.disabled = true;
                return false;
            }
            
            // If we got here, input is valid
            return true;
        }

        // Location modal functions
        document.getElementById('add-location-btn').addEventListener('click', function() {
            document.getElementById('add-location-modal').classList.remove('hidden');
        });

        function closeLocationModal() {
            // Hide the modal
            document.getElementById('add-location-modal').classList.add('hidden');
            
            // Clear the location name input field
            const locationInput = document.getElementById('location');
            if (locationInput) {
                locationInput.value = '';
                
                // Reset any error states
                const errorElement = document.getElementById('location-error');
                if (errorElement) {
                    errorElement.classList.add('hidden');
                    errorElement.textContent = '';
                }
                locationInput.classList.remove('border-red-500');
                
                // Re-enable the submit button if it was disabled
                const submitButton = document.querySelector('#add-location-form button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = false;
                }
            }
        }

        // Close the location modal if clicking outside of it
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('add-location-modal');
            if (event.target === modal) {
                closeLocationModal();
            }
        });

        // Validate the location form before submission
        function validateLocationForm() {
            const locationInput = document.getElementById('location');
            return validateLocationName(locationInput);
        }
        
        // Checker modal functions
        document.getElementById('add-checker-btn').addEventListener('click', function() {
            document.getElementById('add-checker-modal').classList.remove('hidden');
        });

        function closeCheckerModal() {
            // Hide the modal
            document.getElementById('add-checker-modal').classList.add('hidden');
            
            // Clear the checker name input field and reset the location dropdown
            const checkerNameInput = document.getElementById('checker-name');
            const checkerLocationSelect = document.getElementById('checker-location');
            
            if (checkerNameInput) {
                checkerNameInput.value = '';
                checkerNameInput.classList.remove('border-red-500');
                document.getElementById('checker-error').classList.add('hidden');
                document.getElementById('checker-error').textContent = '';
            }
            
            if (checkerLocationSelect) {
                checkerLocationSelect.selectedIndex = 0;
            }
        }

        // Close the checker modal if clicking outside of it
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('add-checker-modal');
            if (event.target === modal) {
                closeCheckerModal();
            }
        });

        // Checker name validation function
        function validateCheckerName(input) {
            const value = input.value;
            const errorElement = document.getElementById('checker-error');
            const submitButton = document.querySelector('#add-checker-form button[type="submit"]');
            
            // Reset error state
            errorElement.classList.add('hidden');
            errorElement.textContent = '';
            input.classList.remove('border-red-500');
            submitButton.disabled = false;
            
            // Check for symbols (anything that's not a letter, number, or space)
            if (/[^a-zA-Z\s]/.test(value)) {
                errorElement.classList.remove('hidden');
                errorElement.textContent = 'Checker name cannot contain numbers or symbols. Only letters and spaces are allowed.';
                input.classList.add('border-red-500');
                submitButton.disabled = true;
                return false;
            }
            
            // If we got here, input is valid
            return true;
        }

        // Validate the checker form before submission
        function validateCheckerForm() {
            const checkerNameInput = document.getElementById('checker-name');
            const checkerLocationSelect = document.getElementById('checker-location');
            const errorElement = document.getElementById('checker-error');
            
            // Check if location is selected
            if (checkerLocationSelect.value === '') {
                errorElement.classList.remove('hidden');
                errorElement.textContent = 'Please select a location for the checker.';
                return false;
            }
            
            return validateCheckerName(checkerNameInput);
        }
    </script>
</section>