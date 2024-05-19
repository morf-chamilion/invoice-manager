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
                    <div class="mb-8">
                        <x-input-label for="page_title" :value="__('Page Title')" />
                        <x-input-editor name="page_title" id="page_title">
                            {{ old('page_title', $settings->get('page_title')) }}
                        </x-input-editor>
                        <x-input-error :messages="$errors->get('page_title')" />
                    </div>

                    <div class="mb-8 col-xl-6">
                        <x-input-label for="banner_image" :value="__('Banner Image')" />
                        <x-input-file id="banner_image" name="banner_image" :fileMaxSize="2" :value="$settings->getMedia('banner_image')" />
                        <x-input-error class="mt-2" :messages="$errors->get('banner_image')" />
                    </div>
                </div>
            </div>

            @include('admin.partials.section-meta')
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
