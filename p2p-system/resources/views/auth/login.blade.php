<x-guest-layout>
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 1rem;">
        @csrf

        <div>
            <label for="login" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Email or Phone Number</label>
            <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus autocomplete="username"
                   placeholder="Enter your email or phone number"
                   style="display: block; width: 100%; font-size: 0.875rem; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white;" />
            <x-input-error :messages="$errors->get('login')" class="mt-2 text-xs" />
        </div>

        <div x-data="{ show: false }">
            <label for="password" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Password</label>
            <div style="position: relative;">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                       style="display: block; width: 100%; font-size: 0.875rem; padding: 0.75rem 1rem; padding-right: 2.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white;" />
                <button type="button" @click="show = !show" style="position: absolute; top: 0; bottom: 0; right: 0; display: flex; align-items: center; padding-left: 0.75rem; padding-right: 0.75rem; color: #6b7280; background: transparent; border: none; cursor: pointer;">
                    <div style="position: relative; width: 1.25rem; height: 1.25rem;">
                        <svg x-show="!show"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-75"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-75"
                             style="position: absolute; inset: 0; width: 100%; height: 100%;"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-75"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-75"
                             style="position: absolute; inset: 0; width: 100%; height: 100%;"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </div>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 0.5rem;">
            <label for="remember_me" style="display: flex; align-items: center;">
                <input id="remember_me" type="checkbox" name="remember" style="width: 1rem; height: 1rem; margin-right: 0.75rem;">
                <span style="font-size: 0.875rem; color: #4b5563;">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link-primary" style="font-size: 0.875rem; text-decoration: underline;">
                    Forgot password?
                </a>
            @endif
        </div>

        <div style="padding-top: 1rem;">
            <button type="submit" class="btn-primary" style="width: 100%; color: white; font-size: 0.875rem; font-weight: 500; padding: 0.75rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer;">
                LOG IN
            </button>
        </div>
    </form>
</x-guest-layout>
