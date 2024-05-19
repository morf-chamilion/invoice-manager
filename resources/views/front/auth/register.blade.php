<x-front-auth-layout>
    <form action="{{ route(FrontAuthRoutePath::REGISTER) }}" method="POST" class="form w-100">
        @csrf

        <div class="text-center mb-11">
            <h1 class="text-dark fw-bolder mb-3">
                {{ __('Register') }}
            </h1>
            <div class="text-gray-500 fw-medium fs-6">
                {{ __('Customer Account Registration') }}
            </div>
        </div>

        <div class="mb-4">
            <x-input-label for="name" :value="__('Name')" required />
            <x-input-text name="name" id="name" :value="old('name')" required />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" required />
            <x-input-text type="email" name="email" id="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="mb-4">
            <x-input-label for="phone" :value="__('Phone')" required />
            <x-input-text name="phone" id="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" />
        </div>

        <div class="mb-4">
            <x-input-label for="address" :value="__('Address')" required />
            <x-input-textarea name="address" id="address" rows="2"
                required>{{ old('address') }}</x-input-textarea>
            <x-input-error :messages="$errors->get('address')" />
        </div>

        <div class="mb-4" data-kt-password-meter="true">
            <x-input-label for="password" :value="__('Password')" required />
            <div class="position-relative mb-3">
                <x-input-text id="password" name="password" type="password" class="mt-1 block w-full"
                    :value="old('password')" required autocomplete="off" />

                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                    data-kt-password-meter-control="visibility">
                    <i class="bi bi-eye-slash fs-2"></i>
                    <i class="bi bi-eye fs-2 d-none"></i>
                </span>
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="mb-4" data-kt-password-meter="true">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" required />
            <div class="position-relative mb-3">
                <x-input-text id="password_confirmation" name="password_confirmation" type="password"
                    class="mt-1 block w-full" :value="old('password_confirmation')" required autocomplete="off" />

                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                    data-kt-password-meter-control="visibility">
                    <i class="bi bi-eye-slash fs-2"></i>
                    <i class="bi bi-eye fs-2 d-none"></i>
                </span>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="mt-8 d-grid">
            <button class="btn btn-primary">
                {{ __('Create Account') }}
            </button>
        </div>

        <div class="mt-8 text-gray-500 text-center fw-semibold fs-6">
            {{ __('Already have an Account?') }}
            <a href="{{ route(FrontAuthRoutePath::LOGIN) }}" class="link-primary">
                {{ __('Login') }}
            </a>
        </div>
    </form>
</x-front-auth-layout>
