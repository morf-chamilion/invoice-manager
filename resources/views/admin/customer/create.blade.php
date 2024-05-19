<x-default-layout>
    <form method="post" action="{{ route(CustomerRoutePath::STORE) }}" autocomplete="off" id="resource_form">
        @method('post')
        @csrf

        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10" id="resource_form_fieldset">
                <div class="card">
                    <div class="card-header">
                        <header>
                            <h2 class="text-lg mt-8 font-medium text-gray-900">
                                {{ __($pageData['title']) }}
                            </h2>
                        </header>
                    </div>
                    <div class="card-body">
                        <div class="mb-8">
                            <x-input-label for="name" :value="__('Name')" required />
                            <x-input-text id="name" name="name" type="text" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="email" :value="__('Email')" required />
                            <x-input-text id="email" name="email" type="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="phone" :value="__('Phone')" required />
                            <x-input-text id="phone" name="phone" type="text" :value="old('phone')" required />
                            <x-input-error :messages="$errors->get('phone')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="address" :value="__('Address')" required />
                            <x-input-textarea id="address" name="address"
                                required>{{ old('address') }}</x-input-textarea>
                            <x-input-error :messages="$errors->get('address')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="company" :value="__('Company')" />
                            <x-input-text id="company" name="company" type="text" :value="old('company')" />
                            <x-input-error :messages="$errors->get('company')" />
                        </div>

                        <div class="mb-8" data-kt-password-meter="true">
                            <x-input-label for="password" :value="__('Password')" required />
                            <div class="position-relative mb-3">
                                <x-input-text id="password" name="password" type="password" class="mt-1 block w-full"
                                    :value="old('password')" autocomplete="none" />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                    data-kt-password-meter-control="visibility">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                    <i class="bi bi-eye fs-2 d-none"></i>
                                </span>
                            </div>
                            <x-input-error :messages="$errors->get('password')" />
                        </div>
                    </div>
                </div>
            </div>

            <x-form-metadata type="Create">
                <div class="mb-10">
                    <x-input-label for="status" required>Status</x-input-label>
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (CustomerStatus::toSelectOptions() as $option)
                            <option value="{{ $option->value }}" @selected($option->value == old('status', CustomerStatus::ACTIVE->value))>
                                {{ $option->name }}
                            </option>
                        @endforeach
                    </x-input-select>
                </div>

                <div class="separator separator-dashed mb-8"></div>

                <div class="mb-8">
                    <div class="d-flex gap-4 align-items-center justify-content-between">
                        <x-input-label for="notification" :value="__('Email Notifications')" />
                        <x-input-checkbox id="notification" name="notification" :value="old('notification')" :value="true" />
                    </div>
                    <p class="text-muted">
                        {{ __('The user will be notified via an email.') }}
                    </p>
                    <x-input-error :messages="$errors->get('notification')" />
                </div>
            </x-form-metadata>

        </div>
    </form>
</x-default-layout>
