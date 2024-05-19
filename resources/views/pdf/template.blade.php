<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>
        {{ settings(SettingModule::GENERAL)->get('site_name') }} | {{ $pageData['title'] ?? '' }}
    </title>

    @stack('header')
</head>

<body style="margin: 0;">
    @yield('content')
</body>

</html>
