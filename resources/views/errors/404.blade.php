<x-system-layout>

    <!--begin::Card-->
    <div class="card card-flush">
        <!--begin::Card body-->
        <div class="card-body">

            <!--begin::Title-->
            <h1 class="fw-bolder fs-2hx text-gray-900 mb-4">
                Page Not Found
            </h1>
            <!--end::Title-->

            <!--begin::Text-->
            <div class="fw-semibold fs-6 text-gray-500 mb-7">
                We can't find that page.
            </div>
            <!--end::Text-->

            <!--begin::Link-->
            <a href="{{ url('') }}" class="mb-0">
                <x-button-primary>{{ __('Return Home') }}</x-button-primary>
            </a>
            <!--end::Link-->

        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

</x-system-layout>
