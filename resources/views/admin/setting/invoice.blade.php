<x-default-layout>
    <form method="post" action="{{ $action }}" autocomplete="off">
        @method('post')
        @csrf

        <div class="d-flex flex-column mb-5">
            <div class="card">
                <div class="card-header">
                    <header>
                        <h2 class="text-lg mt-8 font-medium text-gray-900">
                            {{ __($pageData['title']) }}
                        </h2>
                    </header>
                </div>
                <div class="card-body">
                    <div class="mb-8 col-xl-4">
                        <x-input-label for="logo" :value="__('Logo')" />
                        <x-input-file id="logo" name="logo" :fileMaxSize="2" :value="$settings->getMedia('logo')" />
                        <x-input-error :messages="$errors->get('logo')" />
                    </div>

                    <div class="mb-8">
                        <x-input-label for="company_content" :value="__('Company Content')" />
                        <x-input-editor name="company_content" id="company_content">
                            {{ old('company_content', $settings->get('company_content')) }}
                        </x-input-editor>
                        <x-input-error :messages="$errors->get('company_content')" />
                    </div>

                    <div class="mb-8">
                        <x-input-label for="footer_content" :value="__('Footer Content')" />
                        <x-input-editor name="footer_content" id="footer_content">
                            {{ old('footer_content', $settings->get('footer_content')) }}
                        </x-input-editor>
                        <x-input-error :messages="$errors->get('footer_content')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
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
