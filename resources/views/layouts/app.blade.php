<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">

    <!-- Scripts -->
    <script src="{{ mix('/js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="{{route('index')}}">HR PHP</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="{{ ($pageName == 'temp') ? 'active': ''}}"><a href="{{route('temp')}}">Температура</a></li>
                        <li class="{{ ($pageName == 'table-orders') ? 'active': ''}}"><a href="{{route('table-orders')}}">Заказы</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container" style="margin-top: 30px">
            <div class="page-header">
                <h1>{{ $title }}</h1>
            </div>
            @yield('content')
        </div>
    </div>
</body>
</html>