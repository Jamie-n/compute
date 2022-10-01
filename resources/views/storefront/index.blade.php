@extends('layouts.app')

@section('title', 'Latest Products')

@section('pre-container')
    @include('layouts.partials.hero')
@endsection

@section('content')
    <p class="mb-3">Browse our extensive range of new stock.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

        @foreach($products as $product)
            <x-listing :product="$product"/>
        @endforeach

    </div>
 <x-pagination :models="$products"/>
@endsection
