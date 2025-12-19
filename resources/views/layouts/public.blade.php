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
                                    @if(optional($menu)->items->count())
                                        @foreach ($menu->items as $item)
                                            @if ($item->post_parent == 0)
                                                @php
                                                    $main_instance = $item->instance();
                                                @endphp

                                                <li>
                                                    <a class="menu-item" href="{{ $main_instance->post_name ?? $item->post_name }}">
                                                        {{ $main_instance->post_title ?? $item->title }}
                                                    </a>

                                                    @if($item->children->count())
                                                        <ul>
                                                            @foreach($item->children as $subMenuItem)
                                                                @php
                                                                    $sub_instance = $subMenuItem->instance();
                                                                @endphp

                                                                <li>
                                                                    <a class="menu-item" href="{{ $sub_instance->post_name ?? $subMenuItem->post_name }}">
                                                                        {{ $sub_instance->post_title ?? $subMenuItem->title }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endif
                                        @endforeach
                                    @else
                                        <li>Menu items not found or empty.</li>
                                    @endif

                                    @auth
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
                            <a class="btn-main fx-slide w-100" href="tel:{{ $website_phone ?? null }}"><span>{{ $website_phone ?? null }}</span></a>

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
                 <div class="col-lg-2 col-sm-6">
                    <div class="widget">
                        <img src="{{ $logo_url }}" style="width: 100%;margin-top:0rem" alt="" >
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                    <p>{!! nl2br($website_description) ?? '' !!}</p>

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
            </div>
        </div>

    </footer>

    <div class="float-text show-on-scroll">
        <span><a href="#">Scroll to top</a></span>
    </div>
    <div class="scrollbar-v show-on-scroll"></div>

    <!-- page preloader begin -->
    <div id="de-loader"></div>
    <!-- page preloader close -->

    <!-- Javascript Files
    ================================================== -->
    <script src="{{ asset('frontend/js/vendors.js') }}"></script>
    <script src="{{ asset('frontend/js/designesia.js') }}"></script>

</body>

</html>
</html>