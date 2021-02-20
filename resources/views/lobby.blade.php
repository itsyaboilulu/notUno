@php
    $page = 'lobby';
@endphp
@extends('/layouts/main')
@section('head')
    <script href='resources/js/ajax.min.js'></script>
     <script href='resources/js/timer.min.js'></script>
@endsection
@section('body')

    <div class="chatLayout">
        <lobby
            v-bind:game         = "{{json_encode($game)}}"
            v-bind:admin        = {{json_encode($game->isAdmin())}}
            v-bind:deck         = {{ json_encode($deck) }}
            v-bind:setting      = {{ json_encode($settings) }}
            v-bind:dleaderboard = {{json_encode($leaderboard)}}
        ></lobby>
        <chat
            v-bind:logs=    "{{ json_encode($chat) }}"
            v-bind:user=    "{{ json_encode(Auth::user()->username) }}"
            v-bind:password="{{ json_encode( $game['password'] )}}"
            v-bind:pbp      = "{{ json_encode( $playbyplay ) }}"
        ></chat>
    </div>
@endsection
