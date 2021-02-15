@php
    $cache = '?v=1.9';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="resources/css/main.min.css{{$cache}}" />
        @yield('head')
        <title>Uno</title>
    </head>
    <div class="header">
        @if (Agent::isMobile() && $page != 'login' )
            <div class="flex">
                <img src="resources/img/bars.png" height="30px" width="30px"
                    onclick="showNav()"
                >
                <a href="/"><img class="logo" src="resources/img/logo.png" height="20px" ></a>
                @if ($page != 'home')
                    <img src="resources/img/chat.png" onclick="showChat()" height="40px" width="40px" >
                @else
                    <div style="width: 40px; height:40px;">&nbsp;</div>
                @endif
            </div>
        @else
            <a href="/"><img src="resources/img/logo.png"> </a>
        @endif
    </div>
    @if (Agent::isMobile())
        <div id="navigation" class="close">
            <div class="navigation">
                <ul>
                    <a href="/">
                        <li>
                            <img class="logo"
                                src="resources/img/logo.png"
                                height="50px" >
                        </li>
                    </a>
                    <a href="/"><li> Home </li></a>
                    <a href="hostnew"><li> Host new game </li></a>
                    <form method="POST" id='logout' action="{{ route('logout') }}">
                        @csrf
                        <a onclick="document.getElementById('logout').submit()"><li>Logout</li></a>
                    </form>
                </ul>
            </div>
        </div>
    @else
        @if ($page != 'login')
            <div id="navigation">
                <ul>
                    <a href="/"><li> Home </li></a>
                    <a href="hostnew"><li> Host new game </li></a>
                    <form method="POST" id='logout' action="{{ route('logout') }}">
                        @csrf
                        <a onclick="document.getElementById('logout').submit()"><li>Logout</li></a>
                    </form>
                </ul>
            </div>
        @endif
    @endif
    <body>
        <div id='app'>
            @yield('body')
        </div>
    </body>
    <script src="public/js/app.js{{$cache}}"></script>
    @yield('script')
    @if (Agent::isMobile())
        <script>
            const appHeight = () => {
                const doc = document.documentElement;
                doc.style.setProperty('--vh', `${window.innerHeight}px`);
            }
            window.addEventListener('resize', appHeight);
            appHeight();
            @if ($page != 'home')
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
