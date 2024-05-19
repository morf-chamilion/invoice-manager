<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Begin::SEO Meta -->

    @if (!empty($pageData) && !empty($pageData->get('meta_title')))
        <title>{{ $pageData->get('meta_title') }}</title>
    @else
        <title>{{ settings(SettingModule::GENERAL)->get('site_name') }}</title>
    @endif

    @if (!empty($pageData) && !empty($pageData->get('meta_description')))
        <meta name="description" content="{{ $pageData->get('meta_description') }}">
    @endif

    <link rel="canonical" href="{{ url()->full() }}">

    <!-- End:: SEO Meta -->

    @if (
        $favicon = settings(SettingModule::GENERAL)->getFirstMedia('site_favicon')
            ?->getFullUrl())
        <link rel="icon" href="{{ $favicon }}" />
    @endif

    @stack('header')

    <!-- scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="{{ asset('assets/js/common/core/filepond.js') }}" crossorigin="anonymous"></script>

    <script>
        window.GLOBAL_STATE = {};
        GLOBAL_STATE.COMMON_MEDIA_STORE = "{{ route(CommonMediaRoutePath::STORE) }}";
    </script>
</head>

<body {!! printHtmlClasses('body') !!} {!! printHtmlAttributes('body') !!}>
    @yield('content')

    @stack('footer')
</body>

</html>
