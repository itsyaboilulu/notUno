<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="resources/css/main.min.css" />
        @yield('head')
        <title>Uno</title>
    </head>
    <div class="header">
        @if (Agent::isMobile() && $page != 'login' )
            <div class="flex">
                <img src="resources/img/bars.png" height="40px" width="40px"
                    onclick="showNav()"
                >
                <a href="/"><img class="logo" src="resources/img/logo.png" height="30px" ></a>
                @if ($page != 'home')
                    <img src="resources/img/chat.png" onclick="showChat()" height="50px" width="50px" >
                @else
                    <div style="width: 50px; height:50px; color:red">-</div>
                @endif
            </div>
        @else
            <a href="/"><img src="resources/img/logo.png"> </a>
        @endif
    </div>
    @if (Agent::isMobile())
        <div id="navigation" class="close">
            <div class="navigation">
                <a href="/"><img class="logo" src="resources/img/logo.png" height="30px" ></a>
                <ul>
                    <a href=""><li> Home </li></a>
                    <a href="hostnew"><li> Host new game </li></a>
                    <form method="POST" id='logout' action="{{ route('logout') }}">
                        @csrf
                        <a onclick="document.getElementById('logout').submit()"><li>Logout</li></a>
                    </form>
                <ul>
            </div>
        </div>
    @endif
    <body>
        <div id='app'>
            @yield('body')
        </div>
    </body>
    <script src="public/js/app.js"></script>
    @yield('script')
    @if (Agent::isMobile())
        <script>
            @if ($page != 'home')
                document.getElementById('chat').classList.add('close');
                function showChat (){
                    document.getElementById('chat').classList.toggle('close');
                    document.getElementById('chat').classList.toggle('open');
                };
                document.getElementById('chat').onclick = function(e){
                    if(e.target !== e.currentTarget) return;
                    document.getElementById('chat').classList.toggle('close');
                    document.getElementById('chat').classList.toggle('open');
                }
            @endif
             function showNav (){
                document.getElementById('navigation').classList.toggle('close');
                document.getElementById('navigation').classList.toggle('open');
            };
            document.getElementById('navigation').onclick = function(e){
                if(e.target !== e.currentTarget) return;
                document.getElementById('navigation').classList.toggle('close');
                document.getElementById('navigation').classList.toggle('open');
            }
        </script>
    @endif
</html>
