@php
    $page = 'login';
@endphp
@extends('layouts.main')

@section('body')
        <register v-bind:action='{{ json_encode( route('register') ) }}' v-bind:crf_token='{{ json_encode(  csrf_token() ) }}' ></register>
@endsection
@section('script')
    <script>

    </script>
@endsection

@error('email')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
@error('name')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
