@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="grid grid-cols-12 gap-0 lg:gap-10">
        <div class="col-span-12 lg:col-span-7">
            <x-card title="Order Summary" class="h-fit my-5 lg:my-0">
                <x-slot:body>
                    <table class="table-fixed">
                        <thead>
                        <th class="text-left py-2">Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        </thead>
                        <tbody>
                        @foreach($basket as $basketItem)
                            <tr>
                                <td class="py-2">
                                    {{$basketItem->getProduct()->name}}
                                </td>
                                <td class="text-center">
                                    {{$basketItem->getQuantity()}}
                                </td>
                                <td class="text-center">
                                    £{{$basketItem->getTotalPrice()}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </x-slot:body>
            </x-card>
        </div>

        <div class="col-span-12 lg:col-span-5">
            <x-card title="Shipping Information">
                <x-slot name="body">
                    <livewire:checkout.checkout-card/>
                </x-slot>
            </x-card>
        </div>

    </div>
@endsection
