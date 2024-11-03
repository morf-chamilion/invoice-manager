<x-default-layout>
    <form method="post" action="{{ $action }}" autocomplete="off">
        @method('post')
        @csrf

        <div class="d-flex flex-column mb-5">
            <div class="card mb-5">
                <div class="card-header">
                    <header>
                        <h2 class="text-lg mt-8 font-medium text-gray-900">
                            {{ __($pageData['title']) }}
                        </h2>
                    </header>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="mb-8">
                            <x-input-label for="site_name" :value="__('Site Name')" />
                            <x-input-text id="site_name" name="site_name" type="text" :value="old('site_name', $settings->get('site_name'))" />
                            <x-input-error :messages="$errors->get('site_name')" />
                        </div>

                        <div class="mb-8 col-xl-4">
                            <x-input-label for="site_logo" :value="__('Site Logo')" />
                            <x-input-file id="site_logo" name="site_logo" :fileMaxSize="2" :value="$settings->getMedia('site_logo')" />
                            <x-input-error :messages="$errors->get('site_logo')" />
                        </div>

                        <div class="mb-8 col-xl-4">
                            <x-input-label for="site_favicon" :value="__('Site Favicon')" />
                            <x-input-file id="site_favicon" name="site_favicon" :fileMaxSize="1" :value="$settings->getMedia('site_favicon')" />
                            <x-input-error :messages="$errors->get('site_favicon')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="compile_front_assets" :value="__('Compile Frontend Assets')" />
                            <x-input-switch id="compile_front_assets" name="compile_front_assets" :value="$settings->get('compile_front_assets')" />
                            <x-input-error :messages="$errors->get('compile_front_assets')" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::SEO Options-->
        <div class="accordion accordion-icon-toggle mb-5" id="kt_accordion_setting">
            <x-setting-accordion :name="__('seo_options')" :title="__('SEO Options')" :$settings>
                <div class="mb-8">
                    <x-input-label for="analytics_header_script" :value="__('Analytics Header Script')" />
                    <x-input-textarea id="analytics_header_script"
                        name="analytics_header_script">{{ old('analytics_header_script', $settings->get('analytics_header_script')) }}
                    </x-input-textarea>
                    <x-input-error :messages="$errors->get('analytics_header_script')" />
                </div>

                <div class="mb-8">
                    <x-input-label for="analytics_body_script" :value="__('Analytics Body Script')" />
                    <x-input-textarea id="analytics_body_script"
                        name="analytics_body_script">{{ old('analytics_body_script', $settings->get('analytics_body_script')) }}
                    </x-input-textarea>
                    <x-input-error :messages="$errors->get('analytics_body_script')" />
                </div>
            </x-setting-accordion>
        </div>
        <!--end::SEO Options-->

        <div class="col-lg-12 mt-8">
            <div class="card mb-5">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-4">
                        <x-button-primary>{{ __('Update') }}</x-button-primary>
                    </div>
                </div>
            </div>
        </div>

    </form>
</x-default-layout>
