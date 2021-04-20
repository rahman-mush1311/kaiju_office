<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ config('app.name') }} | @yield('title', 'Dashboard')</title>

   @include('partials.styles')

</head>

<body>
<div id="app">
    <div class="main-wrapper">

    <!-- Main Content -->
        <div class="main-content" style = "padding-left:30px;padding-top:30px;">
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

    </div>
</div>

@include('partials.scripts')
@stack('stack_js')

</body>
</html>
