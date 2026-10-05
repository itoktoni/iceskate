<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ env('APP_NAME') }}</title>
    <link rel="icon" href="{{ asset('frontend/images/favicon.png') }}" type="image/png" sizes="16x16">
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" >
    <meta content="{{ $website_description }}" name="description" >
    <meta content="" name="keywords" >
    <meta content="" name="author" >
    <!-- CSS Files
    ================================================== -->
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" id="bootstrap">
    <link href="{{ asset('frontend/css/plugins.css') }}" rel="stylesheet" type="text/css" >
    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet" type="text/css" >
    <link href="{{ asset('frontend/css/coloring.css') }}" rel="stylesheet" type="text/css" >
    <!-- color scheme -->
    <link id="colors" href="{{ asset('frontend/css/colors/scheme-01.css') }}" rel="stylesheet" type="text/css" >

    <style>
        header{
            /* z-index: 0 !important; */
        }
    </style>

</head>

<body class="light-scheme">

    <!-- header begin -->
    <header class="transparent">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="de-flex">
                        <div class="de-flex-col">
                            <!-- logo begin -->
                            <div id="logo">
                                <a href="{{ url('/') }}">
                                    <img class="logo-main" src="{{ $logo_url }}" alt="" >
                                    <img class="logo-scroll" src="{{ $logo_url }}" alt="" >
                                    <img class="logo-mobile" src="{{ $logo_url }}" alt="" >
                                </a>
                            </div>
                            <!-- logo close -->
                        </div>

                        <div class="de-flex-col">
                            <div class="de-flex-col header-col-mid">
                                <ul id="mainmenu">
                                    @php
                                        $menuTree = [];
                                        try {
                                            if (optional($menu)->items && $menu->items->count()) {
                                                $menuTree = \App\Services\MenuTreeBuilder::toTree(
                                                    \App\Services\MenuTreeBuilder::fromCorcel($menu->items)
                                                );
                                            }
                                        } catch (\Throwable $e) { $menuTree = []; }
                                    @endphp
                                    @if(count($menuTree))
                                        @include('components.menu-nodes', ['nodes' => $menuTree])
                                    @else
                                        <li>Menu items not found or empty.</li>
                                    @endif

                                    @auth
                                    <li>
                                        <a class="menu-item" href="{{ route('payment') }}">
                                            Iuran
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('kehadiran') }}">
                                            Kehadiran
                                        </a>
                                    </li>
                                    <li>
                                        <a class="menu-item" href="{{ route('performance') }}">
                                            Performance
                                        </a>
                                    </li>
                                    <li>
                                         <a class="menu-item" href="{{ route('signout') }}">
                                            Logout
                                        </a>
                                    </li>
                                      @else
                                    <li>
                                         <a class="menu-item" href="{{ route('login') }}">
                                            Member
                                        </a>
                                    </li>
                                    @endauth
                                </ul>
                            </div>
                        </div>

                        <div class="de-flex-col">
                            @guest
                                <a class="btn-main fx-slide w-100" style="z-index: 1 !important" href="tel:{{ $website_phone ?? null }}"><span>{{ $website_phone ?? null }}</span></a>
                            @endguest

                            <div class="menu_side_area">
                                <span id="menu-btn"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header end -->

    <!-- main content begin -->
    <main>

        @yield('content')

    </main>
    <!-- main content end -->

    <footer class="pb-5">
        <div class="container">
            <div class="row ">
                <div class="col-lg-6 col-sm-6">
                   {!! nl2br($website_description) ?? '' !!}
                </div>

                <div class="col-lg-4 col-sm-6 order-lg-2 order-sm-1">
                    <div class="widget">

                        <div class="fw-bold"><i class="icofont-location-pin me-2 id-color"></i>Our Location</div>
                        {{ $website_address }}

                        <div class="spacer-20"></div>

                        <div class="fw-bold"><i class="icofont-envelope me-2 id-color"></i>Send a Message</div>

                        <a href="mailto:{{ $website_email }}">{{ $website_email }}</a>

                    </div>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <div class="widget">
                        <div class="fw-bold">Quick Links</div>
                        @php
                            $footerTree = [];
                            try {
                                if (optional($footerMenu ?? null)->items && ($footerMenu ?? null)->items->count()) {
                                    $footerTree = \App\Services\MenuTreeBuilder::toTree(
                                        \App\Services\MenuTreeBuilder::fromCorcel(($footerMenu ?? null)->items)
                                    );
                                }
                            } catch (\Throwable $e) { $footerTree = []; }
                        @endphp
                        @if(count($footerTree))
                            <ul class="footer-menu-side" style="list-style:none;padding-left:0;margin:0;">
                                @foreach($footerTree as $node)
                                @php
                                    $m = $node['item']['_model'] ?? null;
                                    $inst = null;
                                    try { $inst = $m ? $m->instance() : null; } catch (\Throwable $e) {}
                                    $t = $inst->post_title ?? $m->title ?? $node['item']['title'] ?? 'Menu';
                                    $mu = null;
                                    try { $mu = $m->meta->_menu_item_url ?? null; } catch (\Throwable $e) {}
                                    $sl = $inst->post_name ?? $m->post_name ?? null;
                                    $href = $mu ?: $sl ?: '#';
                                @endphp
                                <li><a href="{{ $href }}" target="_blank" rel="noopener">{{ $t }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </footer>
    <style>
        .footer-menu-side {
            display: block; list-style: none; padding-left: 0; margin: 0;
        }
        .footer-menu-side li { display: block; margin: 3px 0; }
        .footer-menu-side a { text-decoration: none; font-weight: 400; }
    </style>

    <div class="float-text show-on-scroll">
        <span><a href="#">Scroll to top</a></span>
    </div>
    <div class="scrollbar-v show-on-scroll"></div>

    <!-- page preloader begin -->
    <div id="de-loader"></div>
    <!-- page preloader close -->

    <a style="heigh:100px;position:fixed;right:1rem;bottom:1rem;z-index:99" target="_blank" href="https://wa.me/{{ $website_phone ?? null }}" class="wa">
        <img style="height:70px" src="/wa.png" alt="">
    </a>

    <!-- Javascript Files
    ================================================== -->
    <script src="{{ asset('frontend/js/vendors.js') }}"></script>
    <script src="{{ asset('frontend/js/designesia.js') }}"></script>

</body>

</html>
</html>