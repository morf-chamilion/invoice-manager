<x-front-customer-layout>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <header>
                        <h2 class="text-lg mt-8 font-medium text-gray-900">
                            {{ __('Account Information') }}
                        </h2>
                    </header>
                </div>
                <div class="card-body">
                    <form action="{{ route(FrontCustomerRoutePath::UPDATE, $customer) }}" method="POST">
                        @method('put')
                        @csrf

                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Name')" required />
                            <x-input-text id="name" name="name" type="text" :value="old('name', $customer->name)" required />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="email" :value="__('Email')" required />
                            <x-input-text id="email" name="email" type="email" :value="old('email', $customer->email)" readonly
                                required />
                            <x-input-error :messages="$errors->get('email')" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="phone" :value="__('Phone')" required />
                            <x-input-text id="phone" name="phone" type="text" :value="old('phone', $customer->phone)" required />
                            <x-input-error :messages="$errors->get('phone')" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="address" :value="__('Address')" required />
                            <x-input-textarea id="address" name="address" rows="2"
                                required>{{ old('address', $customer->address) }}</x-input-textarea>
                            <x-input-error :messages="$errors->get('address')" />
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <header>
                        <h2 class="text-lg mt-8 font-medium text-gray-900">
                            {{ __('Update Password') }}
                        </h2>
                    </header>
                </div>
                <div class="card-body">
                    <form action="{{ route(FrontAuthRoutePath::PASSWORD_UPDATE) }}" method="POST">
                        @method('put')
                        @csrf

                        <div class="mb-6">
                            <x-input-label for="password" :value="__('New Password')" required />
                            <x-input-text id="password" name="password" type="password" autocomplete="new-password"
                                required />
                            <x-input-error :messages="$errors->updatePassword->get('password')" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" required />
                            <x-input-text id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" required />
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
                        </div>

                        <div class="text-end">
                            <button class="btn btn-primary">{{ __('Update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-front-customer-layout>
