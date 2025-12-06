<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.meta')

<head>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @livewireStyles

    <style>
        .navigation .navigation-menu-tab ul li, .navigation .navigation-menu-tab   {
            background-color: {{ env('APP_COLOR') ?? '#1565C0' }} !important;
        }

        .navigation .navigation-menu-body ul li>a.active{
            background-color: {{ env('APP_COLOR') ?? '#1565C0' }} !important;
        }

        .navigation .navigation-menu-body ul li.open>a{
            color: {{ env('APP_COLOR') ?? '#1565C0' }} !important;
        }

        @media (max-width: 768px) {
            .header {
                background-color: {{ env('APP_COLOR') ?? '#1565C0' }} !important;
            }
        }
    </style>

</head>

<body class="fixed" data-turbo-prefetch="false">

    @include('layouts.header')

    <div id="main">

        <div class="navigation">

            @include('layouts.left')

        </div>

        <div class="main-content" id="content">
            <div class="container-fluid">
                {{ $slot }}
            </div>
        </div>

    </div>

    @stack('footer')

    @include('layouts.alert')

    @livewireScriptConfig

</body>

</html>

