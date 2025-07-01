<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                <button onclick="window.history.back();" class="px-4 py-2 text-blue-600 rounded-md hover:text-blue-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    ←
                </button>
                {{ __('Users') }}
            </h2>

        </div>
    </x-slot>

    <div class="flex items-center justify-between w-full">
        <!-- SEARCH FORM -->
        <form method="GET" class="w-full max-w-xl">
            <div class="flex">
                <input type="text" name="search" placeholder="Search by Name, Role, Email, Location"
                    class="w-full px-4 py-2 border rounded-l-md focus:ring focus:ring-indigo-200 dark:bg-gray-800 dark:text-white">
                <button type="submit" class="px-4 py-2 text-white bg-emerald-500 rounded-r-md hover:bg-emerald-700">
                    SEARCH
                </button>
            </div>
        </form>
    </div>
    <br>

    @if (isset($searchMessage))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800">
            {{ $searchMessage }}
        </div>
    @endif

    <div class="grid gap-4 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 w-full">
        <div class="h-full p-6 overflow-hidden col-span-1 sm:col-span-1 md:col-span-2 lg:col-span-2 xl:col-span-2 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300 dark:border-gray-700">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-800">
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">First Name</th>
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">Last Name</th>
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">Role</th>
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">Email</th>
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2">Location</th>
                        <th class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($users->isEmpty())
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">No user found.</td>
                        </tr>
                    @else
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">{{ $user->fName }}</td>
                                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">{{ $user->lName }}</td>
                                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">{{ $user->roles->roles }}</td>
                                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">{{ $user->email }}</td>
                                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2">{{ $user->location }}</td>
                                <td class="border border-gray-300 dark:border-gray-700 px-4 py-2 text-center">
                                    <button
                                        class="text-blue-600 hover:text-blue-900 items-center max-w-xs gap-2"
                                        x-on:click.prevent="$dispatch('open-modal', 'edit-{{$user->id}}')">
                                        <x-heroicon-o-pencil class="w-6 h-6" />
                                    </button>
                                    <span>|</span>
                                    <button
                                        x-on:click.prevent="$dispatch('open-modal', 'delete-{{$user->id}}')"
                                        class="text-red-600 hover:text-red-900 items-center">
                                        <x-heroicon-o-trash class="w-6 h-6" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
        @foreach ($users as $user)
            <x-modal name="edit-{{$user->id}}" focusable>
                <form method="POST" action="{{ route('users.update', $user->id) }}" class="p-6 bg-white dark:bg-[#313647] rounded-lg shadow-md">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="page" value="{{ request('page', 1) }}">

                    <!-- Header (Edit Item + Close Button) -->
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('EDIT USER') }}
                        </h2>
                        <button @click="$dispatch('close')" type="button"
                            class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-2xl ">
                            &times;
                        </button>
                    </div>

                    <div class="grid gap-4">
                        <!-- FIRST NAME -->
                        <div class="space-y-2">
                            <x-form.label for="fName" :value="__('First Name')" class="text-gray-800 dark:text-white"/>
                            <x-form.input-with-icon-wrapper>
                                <x-slot name="icon">
                                    <x-heroicon-o-user class="w-5 h-5" />
                                </x-slot>
                                <x-form.input
                                    withicon id="fName"
                                    class="block w-full focus:ring focus:ring-indigo-300"
                                    type="text"
                                    name="fName"
                                    value="{{ old('fName', $user->fName) }}"
                                    required
                                />
                            </x-form.input-with-icon-wrapper>
                        </div>

                        <!-- LAST NAME -->
                        <div class="space-y-2">
                            <x-form.label for="lName" :value="__('Last Name')" class="text-gray-800 dark:text-white"/>
                            <x-form.input-with-icon-wrapper>
                                <x-slot name="icon">
                                    <x-heroicon-o-user class="w-5 h-5" />
                                </x-slot>
                                <x-form.input
                                    withicon id="lName"
                                    class="block w-full focus:ring focus:ring-indigo-300"
                                    type="text"
                                    name="lName"
                                    value="{{ old('lName', $user->lName) }}"
                                    required
                                />
                            </x-form.input-with-icon-wrapper>
                        </div>

                        <!-- EMAIL -->
                        <div class="space-y-2">
                            <x-form.label for="email" :value="__('Email')" class="text-gray-800 dark:text-white"/>
                            <x-form.input-with-icon-wrapper>
                                <x-slot name="icon">
                                    <x-heroicon-o-mail class="w-5 h-5" />
                                </x-slot>
                                <x-form.input
                                    withicon id="email"
                                    class="block w-full focus:ring focus:ring-indigo-300"
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    
                                />
                            </x-form.input-with-icon-wrapper>
                        </div>

                        <!-- ROLE -->
                        <div class="space-y-2">
                            <x-form.label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="block w-full px-4 py-2 bg-white dark:bg-[#313647] rounded-lg text-gray-800 dark:text-white focus:ring focus:ring-indigo-300">
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}> -- Select Role -- </option>
                                    <option value="Accountant" {{ $user->roles->roles == 'Accountant' ? 'selected' : '' }}>Accountant</option>
                                    <option value="Admin" {{ $user->roles->roles == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Assistant Manager" {{ $user->roles->roles == 'Assistant Manager' ? 'selected' : '' }}>Assistant Manager</option>
                                    <option value="Bookkeeper" {{ $user->roles->roles == 'Bookkeeper' ? 'selected' : '' }}>Bookkeeper</option>
                                    <option value="IT" {{ $user->roles->roles == 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="Manager" {{ $user->roles->roles == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Pricing" {{ $user->roles->roles == 'Pricing' ? 'selected' : '' }}>Pricing</option>
                                    <option value="Staff" {{ $user->roles->roles == 'Staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                        </div>                    <!-- LOCATION -->
                    <div class="space-y-2">
                        <x-form.label for="location" :value="__('Location')" />
                        <select id="location" name="location" class="block w-full px-4 py-2 bg-white dark:bg-[#313647] rounded-lg text-gray-800 dark:text-white focus:ring focus:ring-indigo-300">
                            <option value="" disabled {{ old('location') ? '' : 'selected' }}> -- Select Location -- </option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->location }}" {{ old('location') == $loc->location ? 'selected' : '' }}>{{ $loc->location }}</option>
                            @endforeach
                        </select>
                    </div>

                        <div>
                            <x-button class="justify-center w-full gap-2">
                                <x-heroicon-o-user-add class="w-6 h-6" />
                                <span>{{ __('Update') }}</span>
                            </x-button>
                        </div>
                    </div>
                </form>
            </x-modal>
        @endforeach

        <div class="p-6 my-3 overflow-hidden col-span-1 bg-white rounded-md shadow-md w-full sm:max-w-full md:max-w-full dark:bg-dark-eval-1">
            <div class="mb-1">
                <span class="font-bold">CREATE USER ACCOUNT</span>
            </div>
            <!-- Validation Errors -->
            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="grid gap-4">
                    <!-- First Name -->
                    <div class="space-y-2">
                        <x-form.label
                            for="fName"
                            :value="__('First Name')"
                        />

                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-user aria-hidden="true" class="w-5 h-5" />
                            </x-slot>

                            <x-form.input
                                withicon
                                id="fName"
                                class="block w-full focus:ring focus:ring-indigo-300"
                                type="text"
                                name="fName"
                                :value="old('fName')"
                                required
                                autofocus
                                placeholder="{{ __('First Name') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                    </div>

                    <!-- Last Name -->
                    <div class="space-y-2">
                        <x-form.label
                            for="lName"
                            :value="__('Last Name')"
                        />

                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-user aria-hidden="true" class="w-5 h-5" />
                            </x-slot>

                            <x-form.input
                                withicon
                                id="lName"
                                class="block w-full focus:ring focus:ring-indigo-300"
                                type="text"
                                name="lName"
                                :value="old('lName')"
                                required
                                autofocus
                                placeholder="{{ __('Last Name') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-2">
                        <x-form.label
                            for="email"
                            :value="__('Email')"
                        />

                        <x-form.input-with-icon-wrapper>
                            <x-slot name="icon">
                                <x-heroicon-o-mail aria-hidden="true" class="w-5 h-5" />
                            </x-slot>

                            <x-form.input
                                withicon
                                id="email"
                                class="block w-full focus:ring focus:ring-indigo-300"
                                type="email"
                                name="email"
                                :value="old('email')"
                                
                                placeholder="{{ __('Email') }}"
                            />
                        </x-form.input-with-icon-wrapper>
                    </div>

                    <!-- Role -->
                    <div class="space-y-2">
                        <x-form.label
                            for="Role"
                            :value="__('Role')"
                        />
                            <div class="relative text-gray-500 focus-within:text-gray-900 dark:focus-within:text-gray-400">
                                <span class="absolute inset-y-0 flex items-center px-4 pointer-events-none">
                                <x-heroicon-o-information-circle aria-hidden="true" class="w-5 h-5" />
                                </span>
                                <select id="role" name="role" class="block w-full pl-10 pr-10 py-2 text-black-500 border-gray-300 rounded focus:border-emerald-300 focus:ring focus:ring-indigo-300 dark:border-gray-600 dark:bg-dark-eval-1">
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}> -- Select Role -- </option>
                                    <option value="Accountant" {{ old('role') == 'Accountant' ? 'selected' : '' }}>Accountant</option>
                                    <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Assistant Manager" {{ old('role') == 'Assistant Manager' ? 'selected' : '' }}>Assistant Manager</option>
                                    <option value="Bookkeeper" {{ old('role') == 'Bookkeeper' ? 'selected' : '' }}>Bookkeeper</option>
                                    <option value="IT" {{ old('role') == 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="Manager" {{ old('role') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Pricing" {{ old('role') == 'Pricing' ? 'selected' : '' }}>Pricing</option>
                                    <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                    </div>

                    <!-- LOCATION -->
                    <div class="space-y-2">
                        <x-form.label for="location" :value="__('Location')" />
                        <select id="location" name="location" class="block w-full px-4 py-2 bg-white dark:bg-[#313647] rounded-lg text-gray-800 dark:text-white focus:ring focus:ring-indigo-300">
                            <option value="" disabled {{ isset($user) && old('location', $user->location) ? '' : 'selected' }}> -- Select Location -- </option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->location }}" {{ isset($user) && old('location', $user->location) == $loc->location ? 'selected' : '' }}>{{ $loc->location }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-button class="justify-center w-full gap-2">
                            <x-heroicon-o-user-add class="w-6 h-6" aria-hidden="true" />

                            <span>{{ __('Register') }}</span>
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br>

    @foreach ($users as $user)
        <x-modal name="delete-{{$user->id}}" focusable>
            <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="p-6 bg-white dark:bg-[#313647] rounded-lg shadow-md">
                @csrf
                @method('DELETE')

                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('Delete User?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Are you sure you want to delete this account? This action cannot be undone.') }}
                </p>

                <div class="mt-4 p-3 bg-white dark:bg-[#313647] rounded-md">
                    <p class="text-sm text-gray-800 dark:text-white">
                        <strong>User ID:</strong> {{ $user->id }}
                    </p>
                    <p class="text-sm text-gray-800 dark:text-white">
                        <strong>Name:</strong> {{ $user->fName }} {{ $user->lName }}
                    </p>
                    <p class="text-sm text-gray-800 dark:text-white">
                        <strong>Role:</strong> {{ $user->roles->roles }}
                    </p>
                    <p class="text-sm text-gray-800 dark:text-white">
                        <strong>Email:</strong> {{ $user->email }}
                    </p>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-button>

                    <x-button variant="danger" class="ml-3">
                        {{ __('Delete Account') }}
                    </x-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-app-layout>
<style>
    .modal-overlay {
        z-index: 150; /* Ensure the modal is above everything */
        background-color: #313647 !important;
    }

    .modal-content {
        z-index: 51; /* Ensure the modal itself is above the overlay */
    }

    .max-w-lg {
        max-width: 32rem; /* Ensures modals are a reasonable size */
    }
    .bg-emerald-600 {
        background-color: #059669 !important; /* Emerald-600 Hex Code */
    }
</style>
