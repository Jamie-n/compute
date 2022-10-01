@extends('layouts.app')

@section('title', $category->name)

@section('content')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-x-10 gap-y-5">

        @foreach($products as $product)
            <x-listing :product="$product"/>
        @endforeach

    </div>
    <x-pagination :models="$products"/>
@endsection
