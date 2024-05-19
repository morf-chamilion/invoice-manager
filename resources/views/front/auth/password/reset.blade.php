<x-front-auth-layout>

    <form action="{{ route(FrontAuthRoutePath::PASSWORD_STORE) }}" method="POST" class="form w-100">
        @csrf

        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">
                {{ __('Change Password') }}
            </h1>
            <!--end::Title-->

            <!--begin::Link-->
            <div class="text-gray-500 fw-semibold fs-6">
                {{ __('Insert your email followed by your new credentials.') }}
            </div>
            <!--end::Link-->
        </div>
        <!--end::Heading-->

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!--begin::Input group--->
        <div class="fv-row mb-8">
            <!--begin::Email-->
            <x-input-label for="email" :value="__('Email')" />
            <x-input-text id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')"
                placeholder="sam@company.com" required autofocus autocomplete="off" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            <!--end::Email-->
        </div>

        <!--begin::Input group-->
        <div class="fv-row mb-8" data-kt-password-meter="true">
            <!--begin::Input wrapper-->
            <div>
                <x-input-label for="password" :value="__('New Password')" />
                <div class="position-relative mb-3">
                    <x-input-text id="password" name="password" type="password" class="mt-1 block w-full"
                        :value="old('password')" placeholder="New password" required autocomplete="off" />

                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                        data-kt-password-meter-control="visibility">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>
            </div>
            <!--end::Input wrapper-->

            <div class="text-muted">
                {{ __('Use 8 or more characters with a mix of letters, numbers & symbols.') }}
            </div>
            <!--end::Hint-->

        </div>
        <!--end::Input group--->

        <!--begin::Input group-->
        <div class="fv-row mb-8" data-kt-password-meter="true">
            <!--begin::Input wrapper-->
            <div>
                <x-input-label for="password-confirmed" :value="__('Confirm Password')" />

                <div class="position-relative mb-3">
                    <x-input-text id="password-confirmed" name="password_confirmation" type="password"
                        class="mt-1 block w-full" :value="old('password_confirmation')" placeholder="New password" required
                        autocomplete="off" />

                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                        data-kt-password-meter-control="visibility">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>
            </div>
            <!--end::Input wrapper-->

            <div class="text-muted">
                {{ __('Repeat the same password as above.') }}
            </div>
            <!--end::Hint-->

        </div>
        <!--end::Input group--->

        <!--begin::Actions-->
        <div class="d-grid">
            <x-button-primary>{{ __('Reset Password') }}</x-button-primary>
        </div>
        <!--end::Actions-->

    </form>

</x-front-auth-layout>
