<x-default-layout :model="$page">
    <form method="POST" action="{{ route(PageRoutePath::UPDATE, $page) }}" autocomplete="off" id="resource_form">
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
                            <x-input-label for="title" :value="__('Title')" />
                            <x-input-text name="title" id="title" :value="old('title', $page->title)" />
                            <x-input-error :messages="$errors->get('title')" />
                        </div>

                        <div class="mb-8">
                            <x-input-label for="slug" :value="__('Slug')" />
                            <x-input-text name="slug" id="slug" :value="old('slug', $page->slug)" />
                            <x-input-error :messages="$errors->get('slug')" />
                        </div>
                    </div>
                </div>
                @yield('template')

                @include('admin.partials.section-meta')
            </div>

            <x-form-metadata :model="$page" type="Update">
                <div class="mb-10">
                    <x-input-label for="status" required>Status</x-input-label>
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (PageStatus::toSelectOptions() as $option)
                            <option value="{{ $option->value }}" @selected($option->value == old('status', $page->status->value))>
                                {{ $option->name }}
                            </option>
                        @endforeach
                    </x-input-select>
                    <x-input-error :messages="$errors->get('status')" />
                </div>

                <div class="mb-10">
                    <x-input-label for="admin_template" required>Template</x-input-label>
                    <x-input-select name="admin_template" data-placeholder="Select Template" data-hide-search="true"
                        required>
                        @foreach ($templates as $key => $template)
                            <option value="{{ $template['admin'] }}" @selected($template['admin'] == old('admin_template', $page->admin_template))>
                                {{ $key }}
                            </option>
                        @endforeach
                    </x-input-select>
                    <p class="text-muted mt-2">Update the form to view template changes.</p>
                    <x-input-error :messages="$errors->get('admin_template')" />
                </div>
            </x-form-metadata>
        </div>
    </form>

    @push('footer')
        @env('local')
        <script>
            {!! file_get_contents(resource_path('js/admin/page.js')) !!}
        </script>
        @endenv

        @env('production')
        <script src="{{ asset('assets/js/admin/page.js') }}"></script>
        @endenv
    @endpush
</x-default-layout>
