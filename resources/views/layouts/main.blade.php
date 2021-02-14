<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="resources/css/main.min.css" />
        @yield('head')
        <title>not-Uno</title>
    </head>
    <div class="header">
        <a href="/"><img src="resources/img/logo.png" ></a>
    </div>
    <body>
        <div id='app'>
            @yield('body')
        </div>
    </body>
    <script src="public/js/app.js"></script>
    @yield('script')
</html>
