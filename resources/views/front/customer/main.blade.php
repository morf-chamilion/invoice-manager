@extends('front.template.main')

@section('content')
    <div id="page">
        @include('front.template.header')

        <main>
            <div id="kt_app_toolbar" class="app-toolbar bg-dark">
                <div class="container d-flex align-items-start">
                    <div class="d-flex flex-column flex-row-fluid">

                        <div class="pt-8 d-flex flex-column justify-content-center flex-wrap me-3">
                            {{ isset($model) ? Breadcrumbs::render(Route::currentRouteName(), $model) : Breadcrumbs::render(Route::currentRouteName()) }}
                        </div>

                        <div class="pt-4">
                            <div class="page-title d-flex align-items-center me-3 mb-4">
                                <h1
                                    class="page-heading d-flex text-white fw-bolder fs-2x flex-column justify-content-center my-0">
                                    {{ $title }}
                                </h1>
                            </div>
                        </div>

                        <div class="mt-10 d-flex justify-content-between flex-wrap gap-4 gap-lg-10">
                            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5">
                                @foreach ($routes as $route => $name)
                                    <li class="nav-item">
                                        <a href="{{ route($route) }}"
                                            class="nav-link text-light text-hover-primary text-active-primary ms-0 me-10 py-4 {{ Route::currentRouteName() == $route ? 'active' : '' }}">
                                            {{ $name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-6 bg-light">
                <div class="mb-5 container">
                    <form id="send-verification" method="post" action="{{ route(FrontAuthRoutePath::VERIFICATION_SEND) }}">
                        @csrf

                        @if ($customer instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$customer->hasVerifiedEmail())
                            <div class="alert bg-warning d-flex flex-column align-items-center flex-sm-row mb-3">
                                <div class="d-flex justify-content-between align-items-center w-100 pe-0 pe-sm-10">
                                    <h5 class="mb-0">
                                        {{ __('Your email address is unverified.') }}
                                    </h5>

                                    @if (session('status') !== 'verification-link-sent')
                                        <button class="theme-btn">
                                            {{ __('Re-send the verification email') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </form>

                    @if (session('status'))
                        <div class="alert bg-info d-flex flex-column align-items-center flex-sm-row mb-3">
                            <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                                <h5 class="mb-0 mt-1 text-light">
                                    @if (session('message'))
                                        {{ session('message') }}
                                    @else
                                        Submission Completed Successfully
                                    @endif
                                </h5>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="container">
                    {{ $slot }}
                </div>
            </div>

        </main>
    </div>

    @include('front.template.footer')
@endsection()

@push('header')
    <!--begin::Fonts-->
    {!! includeFonts() !!}
    <!--end::Fonts-->

    <link rel="stylesheet" href="{{ asset('assets/css/front/master.min.css') }}">

    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    @foreach (getGlobalAssets('css') as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Global Stylesheets Bundle-->

    <!--begin::Vendor Stylesheets(used by this page)-->
    @foreach (getVendors('css') as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Vendor Stylesheets-->

    <!--begin::Custom Stylesheets(optional)-->
    @foreach (getCustomCss() as $path)
        {!! sprintf('<link rel="stylesheet" href="%s">', asset($path)) !!}
    @endforeach
    <!--end::Custom Stylesheets-->

    {{-- styles --}}
    <link rel="stylesheet" href="{{ asset('mmenu/mmenu.css') }}">
@endpush()

@push('footer')
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    @foreach (getGlobalAssets() as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Global Javascript Bundle-->

    <!--begin::Vendors Javascript(used by this page)-->
    @foreach (getVendors('js') as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Vendors Javascript-->

    <!--begin::Custom Javascript(optional)-->
    @foreach (getCustomJs() as $path)
        {!! sprintf('<script src="%s"></script>', asset($path)) !!}
    @endforeach
    <!--end::Custom Javascript-->

    <script src="{{ asset('mmenu/mmenu.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/front/custom-combined.min.js') }}" crossorigin="anonymous" defer></script>

    @env('local')
    <script>
        {!! file_get_contents(resource_path('js/front/customer.js')) !!}
    </script>
    @endenv

    @env('production')
    <script src="{{ asset('assets/js/front/customer.js') }}"></script>
    @endenv
@endpush()
