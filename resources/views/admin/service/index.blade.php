<x-default-layout>
    <div class="card mb-5">
        <div class="card-body">
            @include('admin.partials.data-table')
        </div>
    </div>

    @push('footer')
        @env('local')
        <script>
            {!! file_get_contents(resource_path('js/admin/service.js')) !!}
        </script>
        @endenv

        @env('production')
        <script src="{{ asset('assets/js/admin/service.js') }}"></script>
        @endenv
    @endpush
</x-default-layout>
