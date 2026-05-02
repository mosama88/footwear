<!DOCTYPE HTML>
<html>

<head>
    @include('website.layouts.head')

</head>

<body>

    <div class="colorlib-loader"></div>

    <div id="page">


        @include('website.layouts.navbar')


        @if (Route::current()->uri() == '/')
            @include('website.layouts.slider')
        @endif


        @yield('content')

        @include('website.layouts.footer')

    </div>

    <div class="gototop js-top">
        <a href="#" class="js-gotop"><i class="ion-ios-arrow-up"></i></a>
    </div>

    @include('website.layouts.scripts')

</body>

</html>
