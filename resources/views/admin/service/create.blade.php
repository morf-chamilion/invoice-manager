<x-default-layout>
    <form method="post" action="{{ route(ServiceRoutePath::STORE) }}" autocomplete="off" id="resource_form">
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

                        <div>
                            <x-input-label for="price" :value="__('Price')" />
                            <div class="input-group">
                                <x-input-text id="price" name="price" type="number" :value="old('price')" />
                                <span class="input-group-text">{{ MoneyHelper::currencyCode() }}</span>
                            </div>
                            <x-input-error :messages="$errors->get('price')" />
                        </div>
                    </div>
                </div>
            </div>

            <x-form-metadata type="Create">
                <div class="mb-10">
                    <x-input-label for="status" :value="__('Status')" required />
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (ServiceStatus::toSelectOptions() as $option)
                            <option value="{{ $option->value }}" @selected($option->value == old('status', ServiceStatus::ACTIVE->value))>
                                {{ $option->name }}</option>
                        @endforeach
                    </x-input-select>
                </div>
            </x-form-metadata>

        </div>
    </form>
</x-default-layout>
