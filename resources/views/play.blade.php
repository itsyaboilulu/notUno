@extends('/layouts/main')
@section('head')

@endsection
@section('body')
    <div class="chatLayout">
        <play
            v-bind:game="{{ json_encode($game) }}"
            v-bind:rawhand="{{json_encode($hand)}}"
            v-bind:myturn={{$yourturn}}
            v-bind:mhand={{json_encode($mhand)}}
        ></play>
        <chat
            v-bind:logs=    "{{ json_encode($chat) }}"
            v-bind:user=    "{{ json_encode(Auth::user()->username) }}"
            v-bind:password="{{ json_encode( $game['password'] )}}"
        ></chat>
    </div>

@endsection
@section('script')
@endsection
