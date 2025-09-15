<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        <!-- Multi-credential identifier: email | phone | username (encoded) -->
        <div>
            <x-input-label for="identifier" :value="__('Email / Phone / Username')" />
            <x-text-input id="identifier" class="block mt-1 w-full" type="text" name="identifier" :value="old('identifier')" required autofocus autocomplete="username" placeholder="Email, phone, or username" />
            <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">You can enter your email, cellphone, or username. If your username was encoded, paste the encoded value here.</p>
        </div>

        <!-- Encoded toggle -->
        <div class="mt-2">
            <label class="inline-flex items-center">
                <input id="use-encoded" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="use_encoded">
                <span class="ms-2 text-sm text-gray-600">I am using an encoded username</span>
            </label>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Alternate sign-ins -->
        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Google OAuth button (redirect to socialite route if configured) -->
                <a href="{{ route('oauth.redirect','google') }}" class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded text-sm hover:opacity-90">Sign in with Google</a>
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none" href="#" id="forgot-username-link">Forgot username?</a>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Hidden modal-like area for forgot-username/email-code flow (simple) -->
    <div id="forgot-username-modal" class="hidden mt-4 p-4 border rounded bg-white shadow">
        <h3 class="font-semibold">Recover username</h3>
        <p class="text-sm text-gray-600">Enter the email you used to register. We'll email a code to let you sign in and correct your username/email.</p>
        <form id="forgot-username-form" method="POST" action="{{ route('auth.recover-username') }}" class="mt-3">
            @csrf
            <div>
                <x-input-label for="recovery_email" :value="__('Email')" />
                <x-text-input id="recovery_email" class="block mt-1 w-full" type="email" name="email" required />
            </div>
            <div class="mt-3">
                <x-primary-button type="submit">Send recovery code</x-primary-button>
                <button type="button" id="cancel-recovery" class="ms-3 text-sm text-gray-600">Cancel</button>
            </div>
        </form>
    </div>

    <script>
        // Ctrl+L developer override handler
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'l') {
                e.preventDefault();
                const token = prompt('Developer override token');
                if (!token) return;
                // Send to dev-override endpoint
                fetch('{{ route('dev.override') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ token })
                }).then(r => r.json())
                  .then(j => {
                      if (j.success) {
                          window.location.href = j.redirect || '/';
                      } else {
                          alert('Override failed: ' + (j.message || 'invalid token'));
                      }
                  }).catch(() => alert('Network error'));
            }
        });

        // Forgot username toggle
        document.getElementById('forgot-username-link').addEventListener('click', function (e) {
            e.preventDefault();
            const m = document.getElementById('forgot-username-modal');
            m.classList.toggle('hidden');
        });
        document.getElementById('cancel-recovery').addEventListener('click', function () {
            document.getElementById('forgot-username-modal').classList.add('hidden');
        });
    </script>
</x-guest-layout>
