@php
    $page = 'settings';
@endphp
@extends('/layouts/main')
@section('head')
@endsection
@section('body')
    <settings
        v-bind:settings={{json_encode($settings)}}
    ></settings>
@endsection

@section('script')
    <script>
        (function (p, u, s, h, x)
        {
            p.pushpad = p.pushpad || function ()
            {
                (p.pushpad.q = p.pushpad.q || []).push(arguments)
            };
            h = u.getElementsByTagName('head')[0];
            x = u.createElement('script');
            x.async = 1;
            x.src = s;
            h.appendChild(x);
        })(window, document, 'https://pushpad.xyz/pushpad.js');
        pushpad('init', {{ $pushpad->pid }});
        pushpad('uid', '{{Auth::user()->username}}', '{{ $pushpad->uidSignature(Auth::user()->username) }}');
        pushpad('widget');

    </script>
@endsection
