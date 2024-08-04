<x-default-layout :model="$vendor">

    <form method="POST" action="{{ route(VendorRoutePath::UPDATE, $vendor) }}" autocomplete="off" id="resource_form">
        @method('put')
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
                                :value="old('name', $vendor->name)" autocomplete="none" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="currency" :value="__('Currency')" required />
                            <x-input-text id="currency" name="currency" type="text" :value="old('currency', $vendor->currency)" required />
                            <x-input-error :messages="$errors->get('currency')" />
                        </div>

                        <div>
                            <x-input-label for="invoice_number_prefix" :value="__('Invoice Number Prefix')" required />
                            <x-input-text id="invoice_number_prefix" name="invoice_number_prefix" type="text"
                                :value="old('invoice_number_prefix', $vendor->invoice_number_prefix)" required />
                            <x-input-error :messages="$errors->get('invoice_number_prefix')" />
                        </div>
                    </div>
                </div>
            </div>

            <x-form-metadata :model="$vendor" type="Update">
                <div class="mb-10">
                    <x-input-label for="status" required>Status</x-input-label>
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (VendorStatus::toSelectOptions() as $option)
                            <option value="{{ $option->value }}" @selected($option->value == old('status', $vendor->status->value))>
                                {{ $option->name }}
                            </option>
                        @endforeach
                    </x-input-select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>
            </x-form-metadata>

        </div>
    </form>
</x-default-layout>
