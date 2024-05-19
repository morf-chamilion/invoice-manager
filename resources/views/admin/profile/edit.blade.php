<x-default-layout>
    <form id="send-verification" method="post" action="{{ route(AdminAuthRoutePath::VERIFICATION_SEND) }}">
        @csrf

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
            <div class="alert bg-warning d-flex flex-column align-items-center flex-sm-row p-5 mb-5">
                <!--begin::Icon-->
                {!! getIcon('lock-3', 'fs-2hx me-4 mb-5 mb-sm-0 text-dark') !!}
                <!--end::Icon-->

                <!--begin::Wrapper-->
                <div class="d-flex justify-content-between align-items-center w-100 text-light pe-0 pe-sm-10">
                    <!--begin::Title-->
                    <h5 class="mb-0">{{ __('Your email address is unverified.') }}</h5>
                    <!--end::Title-->

                    <!--begin::Content-->
                    @if (session('status') !== 'verification-link-sent')
                        <x-button-primary class="btn-dark">
                            {{ __('Re-send the verification email') }}
                        </x-button-primary>
                    @endif
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->
            </div>
        @endif
    </form>

    <div class="card mb-5">
        <div class="card-body">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Profile Information') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __("Update your account's profile information and email address.") }}
                    </p>
                </header>

                <form method="post" action="{{ route(AdminRoutePath::PROFILE_UPDATE) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')

                    <div class="fv-row col-lg-6 mb-8">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-input-text id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name', $user->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="fv-row col-lg-6 mb-8">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-input-text id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email', $user->email)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-button-primary>{{ __('Save') }}</x-button-primary>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-body">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Update Password') }}
                    </h2>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Ensure your account is using a long, random password to stay secure.') }}
                    </p>
                </header>

                <form method="post" action="{{ route(AdminAuthRoutePath::PASSWORD_UPDATE) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div class="mb-8" data-kt-password-meter="true">
                        <x-input-label for="password" :value="__('New Password')" />
                        <div class="position-relative mb-3">
                            <x-input-text id="password" name="password" type="password" class="mt-1 block w-full"
                                autocomplete="new-password" />
                            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                data-kt-password-meter-control="visibility">
                                <i class="bi bi-eye-slash fs-2"></i>
                                <i class="bi bi-eye fs-2 d-none"></i>
                            </span>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div class="mb-8" data-kt-password-meter="true">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <div class="position-relative mb-3">
                            <x-input-text id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" />
                            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                data-kt-password-meter-control="visibility">
                                <i class="bi bi-eye-slash fs-2"></i>
                                <i class="bi bi-eye fs-2 d-none"></i>
                            </span>
                        </div>
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-button-primary>{{ __('Save') }}</x-button-primary>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"></div>
    </div>

</x-default-layout>
