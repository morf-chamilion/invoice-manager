<x-default-layout :model="$vendor">

    <form method="POST" action="{{ route(VendorRoutePath::INVOICE_SETTING_UPDATE, $vendor) }}" autocomplete="off"
        id="resource_form">
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
                        <div class="mb-8 col-xl-6">
                            <x-input-label for="logo" :value="__('Logo')" />
                            <x-input-file id="logo" name="logo" :fileMaxSize="2" :value="$vendor->logo" />
                            <x-input-error :messages="$errors->get('logo')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="address" :value="__('Address')" />
                            <x-input-textarea name="address" id="address">
                                {{ old('address', $vendor->address) }}
                            </x-input-textarea>
                            <x-input-error :messages="$errors->get('address')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="footer_content" :value="__('Footer Content')" />
                            <x-input-editor name="footer_content" id="footer_content">
                                {{ old('footer_content', $vendor->footer_content) }}
                            </x-input-editor>
                            <x-input-error :messages="$errors->get('footer_content')" />
                        </div>
                    </div>
                </div>
            </div>

            <x-form-metadata :model="$vendor" type="Update" />

        </div>
    </form>
</x-default-layout>
