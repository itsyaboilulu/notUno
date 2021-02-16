@php
    $page = 'home';
@endphp
@extends('/layouts/main')
@section('head')

@endsection
@section('body')
    <div id='home'>
        <div class="home">
            <h4>Lobbys</h4>
            <div class="lobbys" >
                @foreach ($games as $game)
                    <span class="lobby" onclick="document.location.href = 'lobby?game={{ $game->password }}'" > > {{$game->name}} < </span>
                @endforeach
            </div>
            <hr>
            <a href="hostnew"><button>Host New Game</button></a>
        </div>
    </div>
    <leaderboard
        v-bind:leaderboard = "{{json_encode($leaderboard)}}"
        v-bind:type = "{{json_encode('Global Leaderboard')}}"
    ></leaderboard>
@endsection
@section('script')
@endsection
