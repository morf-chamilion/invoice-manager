<x-default-layout>
    <div class="card mb-5">
        <div class="card-body">
            @include('admin.partials.data-table')
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"></div>
    </div>

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
