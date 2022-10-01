@extends('layouts.app')

@section('title', 'Basket')

@section('content')
    <div class="grid grid-cols-12 gap-0 lg:gap-10">
        @if(CartManager::hasItemsInBasket())
            <x-card title="Order Summary" class="col-span-12 lg:col-span-7 my-5 lg:my-0">
                <x-slot name="body">
                    <x-error-summary bag-name="cart"
                                     summary-message="Unfortunately we could not process your order, the following items are out of stock."
                                     post-summary-message="To Proceed with your purchase please remove these items from the basket."/>

                    <table class="table-fixed">
                        <thead>
                        <th class="text-left py-2">Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th><span class="sr-only">Action</span></th>
                        </thead>
                        <tbody>
                        @foreach($basket as $basketItem)
                            <tr>
                                <td class="py-2">
                                    {{$basketItem->getProduct()->name}} @error($basketItem->getProduct()->id) @enderror
                                </td>
                                <td class="text-center">
                                    {{$basketItem->getQuantity()}}
                                </td>
                                <td class="text-center">
                                    £{{$basketItem->getTotalPrice()}}
                                </td>
                                <td class="text-right p-3 lg:p-0">
                                    @if($basketItem->canAddMoreToBasket())
                                        {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
                                        {{Form::open(['url' => route('basket.add-product', $basketItem->getProduct()), 'class'=>'inline'])}}
                                        <x-button.submit type="submit" class="border-orange-500 px-3 text-orange-500 hover:bg-orange-500 hover:text-white w-full lg:w-auto mb-3 lg:mb-0" title="Increase Quantity">+</x-button.submit>
                                        {{Form::close()}}
                                    @endif

                                    @if($basketItem->getQuantity() > 1)
                                        {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
                                        {{Form::open(['url' => route('basket.reduce-product', $basketItem->getProduct()), 'class'=>'inline'])}}
                                        <x-button.submit type="submit" class="border-orange-500 px-3 text-orange-500 hover:bg-orange-500 hover:text-white w-full lg:w-auto mb-3 lg:mb-0" title="Decrease Quantity">-</x-button.submit>
                                        {{Form::close()}}
                                    @endif

                                    {{-- Form facade used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
                                    {{Form::open(['url' => route('basket.remove-product', $basketItem->getProduct()), 'class'=>'inline'])}}
                                    <x-button.submit type="submit" class="bg-red-500 px-3 text-white hover:bg-red-600 w-full lg:w-auto"><i class="fa-solid fa-trash text-sm" title="Remove {{$basketItem->getProduct()->name}}"></i><span class="sr-only">Remove Button</span></x-button.submit>
                                    {{Form::close()}}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </x-slot>
            </x-card>

            @guest
                <div class="col-span-12 lg:col-span-5">
                    <x-auth.login-card redirect="checkout"/>
                </div>
            @else
                <x-card title="Checkout" class="col-span-12 lg:col-span-5">
                    <x-slot:body>
                        <x-card-subsection class="text-center" hr-bottom>
                            <x-order-total :total="$total"/>
                        </x-card-subsection>
                        <x-card-subsection>
                            <x-button.anchor class="mb-5 w-full transition-colors duration-200 border rounded-full text-center p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white inline-block p-3"
                                             :route="route('checkout.index')">
                                Proceed To Checkout
                            </x-button.anchor>
                        </x-card-subsection>
                    </x-slot:body>
                </x-card>
            @endguest
    </div>
    @else
        <x-card title="Order Summary" class="col-span-12">
            <x-slot:body>
                <x-card-subsection class="text-center">
                    <p class="mb-5">You Have No Items In Your Basket.</p>
                    <a href="{{route('storefront.index')}}" class="text-orange-500 hover:text-orange-600 transition duration-150">Return to Shop</a>
                </x-card-subsection>
            </x-slot:body>
        </x-card>
    @endif

@endsection
