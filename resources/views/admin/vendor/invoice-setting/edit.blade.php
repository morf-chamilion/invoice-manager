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

            <div class="col-lg-3" id="form_metadata_container">
                <div class="card" data-kt-sticky="true" data-kt-sticky-name="form-metadata-card"
                    data-kt-sticky-width="{target: '#form_metadata_container'}" data-kt-sticky-top="100px"
                    data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                    <div class="card-body">

                        @if (!empty($vendor->updatedByUser))
                            <h4 class="form-label">{{ __('Last Updated By') }}</h4>
                            <div class="d-flex justify-content-between mb-4">
                                <p class="text-muted">{{ $vendor?->updatedByUser->name }}</p>
                                <p class="text-muted">{{ $vendor?->updated_at->diffForHumans() }}</p>
                            </div>

                            <div class="separator separator-dashed mb-8"></div>
                        @endif

                        <div class="flex items-center gap-4">
                            <x-button-primary class="w-100">{{ __('Update') }}</x-button-primary>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-default-layout>
