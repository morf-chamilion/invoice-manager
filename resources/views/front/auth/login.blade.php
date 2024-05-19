<x-front-auth-layout>
    <form action="{{ route(FrontAuthRoutePath::LOGIN) }}" method="POST" class="form w-100">
        @csrf

        <div class="text-center mb-11">
            <h1 class="text-dark fw-bolder mb-3">
                {{ __('Login') }}
            </h1>
            <div class="text-gray-500 fw-medium fs-6">
                {{ __('Customer Account Authentication') }}
            </div>
        </div>

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" required />
            <x-input-text id="email" name="email" type="email" :value="old('email')" placeholder="sam@company.com"
                autofocus autocomplete="email" required />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="mb-6" data-kt-password-meter="true">
            <x-input-label for="password" :value="__('Password')" required />
            <div class="position-relative mb-3">
                <x-input-text id="password" name="password" type="password" class="mt-1 block w-full" :value="old('password')"
                    required autocomplete="off" />

                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                    data-kt-password-meter-control="visibility">
                    <i class="bi bi-eye-slash fs-2"></i>
                    <i class="bi bi-eye fs-2 d-none"></i>
                </span>
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" />
            <label class="form-check-label" for="remember">
                {{ __('Remember me') }}
            </label>
        </div>

        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold">
            <div></div>
            <a href="{{ route(FrontAuthRoutePath::PASSWORD_FORGET) }}" class="link-primary">
                {{ __('Forgot Password ?') }}
            </a>
        </div>

        <div class="mt-8 d-grid">
            <button class="btn btn-primary">
                {{ __('Login') }}
            </button>
        </div>

        <div class="mt-8 text-gray-500 text-center fw-semibold fs-6">
            {{ __('Not a Member yet?') }}
            <a href="{{ route(FrontAuthRoutePath::REGISTER) }}" class="link-primary">
                {{ __('Register') }}
            </a>
        </div>
    </form>
</x-front-auth-layout>
