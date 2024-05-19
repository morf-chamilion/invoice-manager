<x-auth-layout>

    <form action="{{ route(AdminAuthRoutePath::PASSWORD_MAIL) }}" method="POST">
        @csrf

        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">
                {{ __('Forgot Password ?') }}
            </h1>
            <!--end::Title-->

            <!--begin::Link-->
            <div class="text-gray-500 fw-semibold fs-6">
                {{ __('Let us know your email and we will send you a password reset link that will allow you to choose a new one.') }}
            </div>
            <!--end::Link-->
        </div>
        <!--end::Heading-->

        <!--begin::Input group--->
        <div class="fv-row mb-8">
            <!--begin::Email-->
            <x-input-label for="email" :value="__('Email')" />
            <x-input-text id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')"
                placeholder="sam@company.com" required autofocus autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            <!--end::Email-->
        </div>

        <!--begin::Actions-->
        <div class="d-flex flex-wrap justify-content-center pb-lg-0 ">
            <a href="{{ route(AdminAuthRoutePath::LOGIN) }}">
                <x-button-secondary class="me-4">{{ __('Cancel') }}</x-button-secondary>
            </a>
            <x-button-primary>{{ __('Submit') }}</x-button-primary>
        </div>
        <!--end::Actions-->
    </form>

</x-auth-layout>
