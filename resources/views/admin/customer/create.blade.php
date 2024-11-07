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
                        <div class="row">
                            <div class="mb-8 col-lg-6">
                                <x-input-label for="name" :value="__('Name')" required />
                                <x-input-text id="name" name="name" type="text" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            <div class="mb-8 col-lg-6">
                                <x-input-label for="email" :value="__('Email')" required />
                                <x-input-text id="email" name="email" type="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" />
                            </div>

                            <div class="mb-8 col-lg-6">
                                <x-input-label for="phone" :value="__('Phone')" required />
                                <x-input-text id="phone" name="phone" type="text" :value="old('phone')" required />
                                <x-input-error :messages="$errors->get('phone')" />
                            </div>

                            <div class="mb-8 col-lg-6">
                                <x-input-label for="company" :value="__('Company')" />
                                <x-input-text id="company" name="company" type="text" :value="old('company')" />
                                <x-input-error :messages="$errors->get('company')" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Address')" required />
                                <x-input-textarea id="address" name="address"
                                    required>{{ old('address') }}</x-input-textarea>
                                <x-input-error :messages="$errors->get('address')" />
                            </div>
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
            </x-form-metadata>

        </div>
    </form>
</x-default-layout>
