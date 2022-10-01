@extends('layouts.app')

@section('content')

    <h2 class="my-3">@yield('code') - @yield('message')</h2>

    <p class="my-3">Something went wrong - <a href="{{route('storefront.index')}}" class="text-orange-400 hover:text-orange-500 hover:underline">return to the homepage.</a></p>

    <p>If you continue to see this error please speak to our support team.</p>

    @if(\Sentry\SentrySdk::getCurrentHub()->getLastEventId())
        <script
            src="https://browser.sentry-cdn.com/7.25.0/bundle.min.js"
            integrity="sha384-sAWci+OD+xB6LxmUoPyE7zbMBw5MAcZtJLLex1A10obajVIyDBDG3ZOP1KE0nj5Q"
            crossorigin="anonymous"
        ></script>

        <script>
            Sentry.init({dsn: "{{config('sentry.dsn')}}"});
            Sentry.showReportDialog({
                eventId: "{{\Sentry\SentrySdk::getCurrentHub()->getLastEventId()}}",
            });
        </script>
    @endif
@endsection
