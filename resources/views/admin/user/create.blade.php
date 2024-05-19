<x-default-layout>
    <form method="post" action="{{ route(UserRoutePath::STORE) }}" autocomplete="off" id="resource_form">
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
                            <x-input-text id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="email" :value="__('Email')" required />
                            <x-input-text id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div class="mb-8" data-kt-password-meter="true">
                            <x-input-label for="password" :value="__('Password')" required />
                            <div class="position-relative mb-3">
                                <x-input-text id="password" name="password" type="password" class="mt-1 block w-full"
                                    :value="old('password')" required />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                    data-kt-password-meter-control="visibility">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                    <i class="bi bi-eye fs-2 d-none"></i>
                                </span>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="role" :value="__('Role')" required />
                            <x-input-select id="role" name="role" data-placeholder="Select Role" required>
                                @if ($roles->isNotEmpty())
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected(old('role'))>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No Roles</option>
                                @endif
                            </x-input-select>
                            <x-input-error class="mt-2" :messages="$errors->get('roles')" />
                        </div>
                    </div>
                </div>
            </div>

            <x-form-metadata type="Create">
                <div class="mb-10">
                    <x-input-label for="status" required>Status</x-input-label>
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (UserStatus::toSelectOptions() as $option)
                            <option value="{{ $option->value }}" @selected($option->value == old('status', UserStatus::ACTIVE->value))>
                                {{ $option->name }}</option>
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
