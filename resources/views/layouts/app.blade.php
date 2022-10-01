<!doctype html>
<html lang="en">
<head>
    <title>{{ config('app.name', 'Compute') }}</title>
    @include('layouts.partials.head')
</head>
<body class="bg-gray-100">
<div>
    @include('layouts.partials.header')

    @include('layouts.partials.nav')
</div>

<main>
        @yield('pre-container')

        <div class="container mx-auto my-10 px-5 lg:px-0">
            <x-alert key="status" class="bg-green-300"/>
            <x-alert class="bg-green-300" :key="\App\Support\Enums\Alert::SUCCESS->value"/>
            <x-alert class="bg-red-300" :key="\App\Support\Enums\Alert::DANGER->value"/>

            @hasSection('title')
                <h2 class="font-bold text-3xl my-5 text-center lg:text-left">@yield('title')</h2>
            @endif

            @yield('content')

        </div>
</main>

<footer>
    @include('layouts.partials.footer')
</footer>

@include('layouts.partials.scripts')

</body>
</html>
