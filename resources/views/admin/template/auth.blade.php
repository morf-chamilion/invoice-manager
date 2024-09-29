@extends('admin.template.master')

@section('content')
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Aside-->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bg-light-primary order-1">
                <!--begin::Content-->
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">

                    <!--begin::Title-->
                    @if ($logo = settings(SettingModule::GENERAL)->getFirstMedia('site_logo'))
                        <img src="{{ $logo?->getFullUrl() }}" style="max-width: 350px;" />
                    @else
                        <h1 class="fs-3x text-primary">
                            {{ settings(SettingModule::GENERAL)->get('site_name') ?? env('APP_NAME') }}
                        </h1>
                    @endif
                    <!--end::Title-->

                </div>
                <!--end::Content-->
            </div>
            <!--end::Aside-->

            <!--begin::Body-->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">

                @include('admin.layout.alert')

                <!--begin::Form-->
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <!--begin::Wrapper-->
                    <div class="w-lg-500px p-10">
                        <!--begin::Page-->
                        {{ $slot }}
                        <!--end::Page-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Form-->

                <!--begin::Footer-->
                <div class="d-flex flex-center flex-wrap px-5"></div>
                <!--end::Footer-->
            </div>
            <!--end::Body-->

        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::App-->
@endsection
