<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage" dir="{{ app()->getLocale() == 'ar'? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ $general->siteName(__($pageTitle)) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.seo')

    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/fontawesome-all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/swiper.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet" />
    <link href="{{ asset($activeTemplateTrue . 'css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/style.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/bootstrap-fileinput.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" rel="stylesheet">

    @stack('style-lib')
    @stack('style')

    <link href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ $general->base_color }}&secondColor={{ $general->secondary_color }}" rel="stylesheet">
</head>

<body @stack('context')>

    @if (@$type != 'app')
        @stack('fbComment')
        <a class="scrollToTop" href="#"><i class="las la-angle-double-up"></i></a>

        @include($activeTemplate . 'partials.preloader')
        @include($activeTemplate . 'partials.header')

        @if (!request()->routeIs('home'))
            @include($activeTemplate . 'partials.breadcrumb')
        @endif
    @endif


    @yield('app')

    @if (@$type != 'app')
        <div class="modal alert-modal fade" id="notifyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div>

        @include($activeTemplate . 'partials.footer')
    @endif

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/global.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/swiper.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.syotimer.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/syotimer.lang.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>
    <script src="{{ asset('assets/global/js/pusher.min.js') }}"></script>


    @stack('script-lib')
    @include('partials.notify')
    @include('partials.plugins')
    @stack('script')

    <script>
        (function($) {
            "use strict";

            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            var inputElements = $('[type=text],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox') {
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach((row) => {
                    Array.from(row.querySelectorAll('td')).forEach((column, i) => {
                        (column.colSpan == 100) || column.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });
        })(jQuery);
    </script>

    @include('partials.push_config')
</body>

</html>
