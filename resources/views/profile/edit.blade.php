<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg dark:bg-gray-800">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg dark:bg-gray-800">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        @php
            $userRole = Auth::user()->roles ? strtoupper(trim(Auth::user()->roles->roles)) : '';
            $isAdmin = in_array($userRole, ['ADMIN', 'ADMINISTRATOR']);
        @endphp
        
        @if ($isAdmin)
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg dark:bg-gray-800">
            <div class="max-w-6xl">
                @include('profile.partials.update-permission-form')
            </div>
        </div>
        @endif
        <br>

        <!--<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg dark:bg-gray-800">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    -->
    </div>
</x-app-layout>
