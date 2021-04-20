<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-115149029-12"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-115149029-12');
    gtag('set', {'user_id': '{{auth_user()->email}}'});
    @if (auth_user()->distributor)
        gtag('set', {'dimension1':'Distributor'});
    @elseif (auth_user()->sr)
        gtag('set', {'dimension1':'Sales Representative'});
    @else
        gtag('set', {'dimension1':'Admin'});
    @endif
    </script>

    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ config('app.name') }} | @yield('title', 'Dashboard')</title>

   @include('partials.styles')

</head>

<body>
<div id="app">
    <div class="main-wrapper">
    @include('partials.header')

    @include('partials.sidebar')

    <!-- Main Content -->
        <div class="main-content">
            <section class="section">
                <div class="section-header">
                    <h1>@yield('heading', 'Dashboard')</h1>

                    <div class="buttons" style="margin: 0 0 -10px 20px;">
                        @yield('heading_buttons')
                    </div>

                    @yield('breadcrumbs')

                </div>
                @include('partials.alert')
                <div class="section-body">
                    @yield('contents')
                </div>
            </section>
        </div>

        @include('partials.footer')
    </div>
</div>

@include('partials.scripts')
@stack('stack_js')

</body>
</html>
