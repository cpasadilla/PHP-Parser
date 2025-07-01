<x-guest-layout>
    <x-auth-card>
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="grid gap-6">
                <!-- FIRST AND LAST Name -->
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
                            class="block w-full"
                            type="text"
                            name="fName"
                            :value="old('fName')"
                            required
                            autofocus
                            placeholder="{{ __('First Name') }}"
                        />
                    </x-form.input-with-icon-wrapper>
                </div>

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
                            class="block w-full"
                            type="text"
                            name="lName"
                            :value="old('lName')"
                            required
                            autofocus
                            placeholder="{{ __('Last Name') }}"
                        />
                    </x-form.input-with-icon-wrapper>
                </div>

                <!-- Phone Number -->
                <div class="space-y-2">
                    <x-form.label
                        for="phone"
                        :value="__('Phone Number')"
                    />

                    <x-form.input-with-icon-wrapper>
                        <x-slot name="icon">
                            <x-heroicon-o-phone aria-hidden="true" class="w-5 h-5" />
                        </x-slot>

                        <x-form.input
                            withicon
                            id="phone"
                            class="block w-full"
                            type="text"
                            name="phone"
                            :value="old('phone')"
                            required
                            placeholder="{{ __('Phone Number') }}"
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
                            class="block w-full"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
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
                        <div class="relative mt-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            </span>
                            <select id="role" name="role" class="block w-full pl-10 pr-10 py-2 text-black-500 border-gray-300 rounded focus:border-emerald-300 focus:ring focus:ring-emerald-500 dark:border-gray-600 dark:bg-dark-eval-1 dark:focus:ring-offset-dark-eval-1">
                                <option disabled selected value> -- Select Role -- </option>
                                <option value="manager">Manager</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                </div>

                <div>
                    <x-button class="justify-center w-full gap-2">
                        <x-heroicon-o-user-add class="w-6 h-6" aria-hidden="true" />

                        <span>{{ __('Register') }}</span>
                    </x-button>
                </div>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
