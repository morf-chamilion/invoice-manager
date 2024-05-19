<x-default-layout>
    <form method="post" action="{{ route(UserRoleRoutePath::STORE) }}" autocomplete="off" id="resource_form">
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

                        <div class="mb-8">
                            <x-input-label for="permissions" :value="__('Permissions')" required />
                            <input type="hidden" name="permissions" id="permissions">
                            <div id="kt_jstree" class="pt-2">
                                <ul>
                                    @foreach ($permissionGroups as $resource => $actions)
                                        <li class="text-dark">
                                            {{ str($resource)->title() }}
                                            <ul>
                                                @foreach ($actions as $action => $permissions)
                                                    <li id="@json(collect($permissions)->pluck('id'))">
                                                        {{ $action }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('permissions')" />
                        </div>

                    </div>
                </div>
            </div>


            <x-form-metadata type="Create">
                <div class="mb-10">
                    <x-input-label for="status" required>Status</x-input-label>
                    <x-input-select name="status" data-placeholder="Select Status" data-hide-search="true" required>
                        @foreach (UserRoleStatus::toSelectOptions() as $option)
                            <option value="{{ $option->value }}" @selected($option->value == old('status', UserRoleStatus::ACTIVE->value))>
                                {{ $option->name }}</option>
                        @endforeach
                    </x-input-select>
                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                </div>
            </x-form-metadata>

        </div>
    </form>

    @php
        addVendor('jstree');
    @endphp

    @push('footer')
        @env('local')
        <script>
            {!! file_get_contents(resource_path('js/admin/user-role.js')) !!}
        </script>
        @endenv

        @env('production')
        <script src="{{ asset('assets/js/admin/user-role.js') }}"></script>
        @endenv
    @endpush
</x-default-layout>
