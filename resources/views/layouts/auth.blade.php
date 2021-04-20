<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ config('app.name') }} | Login</title>

    @include('partials.styles')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-social.css') }}">

</head>

<body>
<div id="app">
    @include('partials.alert')

    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="login-brand">
                        <img src="https://m.deligram.com/svg/icons/icon-dg.svg" alt="logo" width="100" class="shadow-light rounded-circle">
                    </div>

                    <div class="card card-primary">
                        <div class="card-header"><h4>@yield('heading', 'Auth')</h4></div>
                        <div class="card-body">
                            @yield('contents')
                        </div>
                    </div>


                    <div class="simple-footer">
                        Copyright &copy; Deligram 2020
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@include('partials.scripts')
</body>
</html>
