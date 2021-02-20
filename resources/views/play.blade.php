@php
    $page = 'play';
@endphp
@extends('/layouts/main')
@section('head')

@endsection
@section('body')
    <div class="chatLayout">
        <play
            v-bind:game="{{ json_encode($game) }}"
            v-bind:rawhand="{{json_encode($play->getHand())}}"
            v-bind:myturn={{($play->checkTurn())?1:0}}
            v-bind:mhand={{json_encode($mhand)}}
        ></play>
        <chat
            v-bind:logs     = "{{ json_encode($chat) }}"
            v-bind:user     = "{{ json_encode(Auth::user()->username) }}"
            v-bind:password = "{{ json_encode( $game['password'] )}}"
            v-bind:pbp      = "{{ json_encode( $playbyplay ) }}"
        ></chat>
    </div>

@endsection
@section('script')
@endsection
