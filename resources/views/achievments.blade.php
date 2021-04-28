@php
    $page = 'achievement';
@endphp
@extends('/layouts/main')
@section('body')
    <achievments
        v-bind:all  ='{{ json_encode( $all ) }}'
        v-bind:got  ='{{ json_encode( $achieved ) }}'
    ></achievments>
@endsection
