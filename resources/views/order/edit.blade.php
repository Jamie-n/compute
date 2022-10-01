@extends('layouts.app')

@section('content')
    <x-card title="Update Shipping Information" title-sub="Please Update all information as required, once you are satisfied with your changes please ensure that you click the 'Update Order' button to save.">
        <x-slot name="body">
            <x-error-summary bag-name="checkout" summary-message="Unfortunately we could not update your shipping information." post-summary-message="To Proceed with updating this information please resolve all errors."/>

            {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
            {{Form::model($order->deliveryAddress, ['url' => route('order.update', $order), 'method' => 'patch'])}}
            @include('checkout._form')
            <x-button.submit type="submit" class="p-3 border-orange-500 text-orange-500 hover:border-orange-600 hover:text-orange-600">Update Order</x-button.submit>
            {{Form::close()}}
        </x-slot>
    </x-card>
@endsection
