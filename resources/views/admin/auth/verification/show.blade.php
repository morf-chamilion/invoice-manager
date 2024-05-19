<x-auth-layout>
    <div class="text-center mb-11">
        <!--begin::Title-->
        <h1 class="text-dark fw-bolder">
            {{ __('Account Verification Status') }}
        </h1>
    </div>

    @isset($message)
        <p class="text-center fw-medium">
            {{ $message }}
        </p>
    @else
        <p class="text-center">
            {{ __('Verification status is unkown.') }}
        </p>
    @endisset

    @if (isset($verified) && $verified)
        <div class="separator my-12"></div>

        <div class="d-grid mb-10">
            <a href="{{ route(AdminAuthRoutePath::LOGIN) }}" class="w-100">
                <x-button-primary class="w-100">{{ __('Login') }}</x-button-primary>
            </a>
        </div>
    @endif
</x-auth-layout>
