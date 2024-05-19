<x-front-auth-layout>
    <form action="{{ route(FrontAuthRoutePath::PASSWORD_MAIL) }}" method="POST" class="form w-100">
        @csrf

        <div class="text-center mb-11">
            <h1 class="text-dark fw-bolder mb-3">
                {{ __('Forgot Password') }}
            </h1>
            <div class="text-gray-500 fw-medium fs-6">
                {{ __('Let us know your email and we will send you a password reset link that will allow you to choose a new one.') }}
            </div>
        </div>

        <div class="mb-6">
            <x-input-label for="email" :value="__('Email')" />
            <x-input-text id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')"
                placeholder="sam@company.com" required autofocus autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="form-group text-center">
            <button class="btn btn-primary">{{ __('Submit') }}</button>
        </div>
    </form>
</x-front-auth-layout>
