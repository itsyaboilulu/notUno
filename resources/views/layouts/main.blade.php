@php
    $cache = '?v='.App\Models\useful::cssUpdateTime();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-J2GEXL7DKE"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'G-J2GEXL7DKE');
            </script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="resources/css/main.min.css{{$cache}}" />
        @yield('head')
        @if ($page == 'play')
            <title>Uno - {{session('game')->name}}</title>
        @else
            <title>Uno - {{$page}}</title>
        @endif
        <link rel="manifest" href="resources/pwa/manifest.json">
        <link rel="icon" href="resources/pwa/maskable_icon.png">
    </head>
    <div class="header">
        @if (Agent::isMobile() && $page != 'login' )
            <div class="flex">
                <img src="resources/img/bars.png" height="30px" width="30px"
                    onclick="showNav()"
                >
                <a href="/"><img class="logo" src="resources/img/logo.png" height="20px" ></a>
                @if ($page != 'home' && $page !='stats' && $page != 'settings' && $page != 'achievement')
                    <img src="resources/img/chat.png" onclick="showChat()" height="32px" width="32px" >
                @else
                    <div style="width: 23px; height:32px;">&nbsp;</div>
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
                    <a href="stats"><li> Stats </li></a>
                    <a href="achievements"><li> Achievements</li> </a>
                    <a href="settings"><li> Settings </li></a>
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
                    <a href="/" @if($page =='home') class="active" @endif ><li> Home </li></a>
                    <a href="hostnew"><li> Host new game </li></a>
                    <a href="stats" @if($page =='stats') class="active" @endif ><li> Stats </li></a>
                    <a href="achievements" @if($page =='achievement') class="active" @endif ><li> Achievements </li> </a>
                    <a href="settings" @if($page =='settings') class="active" @endif ><li> Settings </li></a>
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
    @if (app()->environment() != 'local')
        <script>
            if (location.protocol == 'https:') {
                location.replace(`http:${location.href.substring(location.protocol.length)}`);
            }
        </script>
    @endif

    @if (Agent::isMobile())
        <script>
            const appHeight = () => {
                const doc = document.documentElement;
                doc.style.setProperty('--vh', `${window.innerHeight}px`);
            }
            window.addEventListener('resize', appHeight);
            appHeight();
            @if ($page != 'home' && $page !='stats' && $page != 'settings' && $page != 'achievement')
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
