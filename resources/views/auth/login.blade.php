@php
    $page = 'login';
@endphp
@extends('../layouts/main')
@section('head')

@endsection
@section('body')
        <login
            v-bind:action='{{ json_encode( route('login') ) }}'
            v-bind:crf_token='{{ json_encode(  csrf_token() ) }}'
            v-bind:login=  '{{json_encode(old('remember'))}}'
        ></login>
@endsection
@section('script')
    <script>

    </script>
@endsection


