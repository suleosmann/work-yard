<x-layouts::pm-auth :title="__('Log in')">
    <div class="mobile-brand">
        <span class="brand-mark"></span>
        <span class="brand-name">WORKYARD</span>
    </div>

    <h1>Log in</h1>
    <p class="lead">Enter your email and password to pick up your tickets.</p>

    @if (session('status'))
        <div class="auth-status" style="margin-bottom:16px;">{{ session('status') }}</div>
    @endif

    @error('email')
        <div class="auth-alert" style="margin-bottom:16px;">{{ $message }}</div>
    @enderror

    {{-- Passkey sign-in (shown only when the browser supports it) --}}
    @assets
        @vite('resources/js/passkeys.js')
    @endassets
    <div
        x-cloak
        x-data="{
            supported: false,
            loading: false,
            error: null,
            init() {
                this.supported = Boolean(window.Passkeys?.isSupported());
                window.addEventListener('passkeys:ready', () => {
                    this.supported = Boolean(window.Passkeys?.isSupported());
                }, { once: true });
            },
            async verify() {
                this.loading = true;
                this.error = null;
                try {
                    const response = await window.Passkeys.verify({
                        routes: {
                            options: '{{ route('passkey.login-options') }}',
                            submit: '{{ route('passkey.login') }}',
                        },
                    });
                    window.location.assign(response.redirect || '{{ route('projects.index') }}');
                } catch (e) {
                    if (e.constructor?.name !== 'UserCancelledError') {
                        this.error = e.message;
                    }
                } finally {
                    this.loading = false;
                }
            },
        }"
    >
        <template x-if="supported">
            <div>
                <button
                    type="button"
                    class="btn btn-ghost btn-block"
                    x-on:click="verify()"
                    x-bind:disabled="loading"
                >
                    <span x-show="!loading">{{ __('Sign in with a passkey') }}</span>
                    <span x-show="loading" x-cloak>{{ __('Authenticating…') }}</span>
                </button>
                <p x-show="error" x-text="error" x-cloak class="auth-alert" style="margin-top:10px;"></p>
                <div class="auth-sep">{{ __('or continue with email') }}</div>
            </div>
        </template>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="auth-form">
        @csrf

        <div class="field">
            <label for="email">{{ __('Email address') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                placeholder="you@example.com"
            >
        </div>

        <div class="field">
            <label for="password">{{ __('Password') }}</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
            >
        </div>

        <div class="auth-row">
            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                {{ __('Remember me') }}
            </label>
            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}" wire:navigate>{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary btn-block" data-test="login-button">
            {{ __('Log in') }}
        </button>
    </form>
</x-layouts::pm-auth>
