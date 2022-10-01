@extends('layouts.app')

@section('content')
    <x-card title="Viewing Order: {{$order->reference_number}}">
        <x-slot name="body">
            <table class="table-fixed w-full">
                <thead>
                <th class="text-left py-2">Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                </thead>
                <tbody>
                @foreach($order->products as $product)
                    <tr>
                        <td class="py-2">
                            @if($product->trashed())
                                <p title="This product is no longer stocked by {{config('app.name')}}">{{$product->name}}</p>
                            @else
                                <a href="{{route('product.show', $product)}}" class="text-orange-400 hover:text-orange-500 hover:underline">{{$product->name}}</a>
                            @endif
                        </td>
                        <td class="text-center">{{$product->pivot->quantity}}</td>
                        <td class="text-center">£{{$product->pivot->unit_price}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <p class="font-bold mt-5">Order Total: £{{$order->totalCost()}}</p>
        </x-slot>
    </x-card>
@endsection
