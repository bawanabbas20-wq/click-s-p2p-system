<x-app-layout>
    <x-slot name="header">
        {{ __('Create New User') }}
    </x-slot>

    <div class="bg-white dark:bg-gray-800 shadow-lg sm:rounded-2xl max-w-2xl mx-auto">
        <div class="p-6 sm:p-8 text-gray-900 dark:text-gray-100">
            <x-input-error :messages="$errors->all()" class="mb-4" />

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                </div>

                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                </div>

                <div class="mt-4">
                    <x-input-label for="phone" :value="__('Phone Number (Optional)')" />
                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" placeholder="e.g., +1234567890" />
                </div>

                <div class="mt-4">
                    <x-input-label for="role" :value="__('Role')" />
                    <select name="role" id="role" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-brand-green focus:ring focus:ring-brand-green/20">
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>{{ __('Employee') }}</option>
                        <option value="procurement" {{ old('role') == 'procurement' ? 'selected' : '' }}>{{ __('Procurement') }}</option>
                        <option value="finance" {{ old('role') == 'finance' ? 'selected' : '' }}>{{ __('Finance') }}</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>{{ __('Manager') }}</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                    </select>
                </div>

                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 me-4">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button>
                        {{ __('Create User') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
