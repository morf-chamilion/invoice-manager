<x-default-layout :model="$vendor">

    <form method="POST" action="{{ route(VendorRoutePath::QUOTATION_SETTING_UPDATE, $vendor) }}" autocomplete="off"
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
                        <div class="mb-8">
                            <x-input-label for="quotation_footer_content" :value="__('Footer Content')" />
                            <x-input-editor name="quotation_footer_content" id="quotation_footer_content">
                                {{ old('quotation_footer_content', $vendor->quotation_footer_content) }}
                            </x-input-editor>
                            <x-input-error :messages="$errors->get('quotation_footer_content')" />
                        </div>

                        <div>
                            <x-input-label for="quotation_terms_of_service" :value="__('Terms of Service')" />
                            <x-input-editor name="quotation_terms_of_service" id="quotation_terms_of_service">
                                {{ old('quotation_terms_of_service', $vendor->quotation_terms_of_service) }}
                            </x-input-editor>
                            <x-input-error :messages="$errors->get('quotation_terms_of_service')" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3" id="form_metadata_container">
                <div class="form-metadata-sticky-container" data-kt-sticky="true"
                    data-kt-sticky-name="form-metadata-card" data-kt-sticky-width="{target: '#form_metadata_container'}"
                    data-kt-sticky-top="100px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                    <div class="card">
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
        </div>
    </form>
</x-default-layout>
