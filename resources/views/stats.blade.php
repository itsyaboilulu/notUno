@php
    $page = 'stats';
@endphp
@extends('/layouts/main')
@section('body')
    <stats
        v-bind:stats='{{ json_encode( $stats->pageData() ) }}'
    ></stats>
@endsection
