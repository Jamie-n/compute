<meta charset="utf-8">
<meta name="description" content="Compute - A Computing Centric eCommerce Platform.">
<meta name="keywords" content="computer, gaming, laptop, desktop, components, peripherals">
<meta name="author" content="Jamie Neighbours">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">

<link rel="icon" type="image/x-icon" href="{{asset('favicon.png')}}">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@vite('resources/css/app.css')

<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet"/>

<style>
    [x-cloak] {
        display: none !important;
    }

    html, body {
        max-width: 100%;
        overflow-x: hidden;
    }
</style>

@livewireStyles

<!-- Stylesheets -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
