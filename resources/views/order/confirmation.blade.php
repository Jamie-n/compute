@extends('layouts.app')

@section('content')
    <x-card title="Order Confirmed">
        <x-slot name="body">
            <p class="mb-4">Thank for shopping with {{config('app.name')}}. </p>

            <p class="mb-4">Your order reference number is: {{Session::get('reference_number')}}, this will be displayed on your paypal invoice.</p>

            <p>You can make updates to this order through the <a href="{{route('order.index')}}" class="text-orange-500 hover:text-orange-600 transition duration-150">order management page</a>.</p>
        </x-slot>
    </x-card>
@endsection
