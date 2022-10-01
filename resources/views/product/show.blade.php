@extends('layouts.app')


@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        <img src="{{asset($product->getProductImageUrl())}}" class="img-fluid" alt="{{$product->name}}">

        <x-card :title="$product->name" :title-sub="$product->brand->name">
            <x-slot name="body">
                <div class="h-full flex flex-col justify-between">

                    <p class="mb-5 lg:mb-0">{{$product->description}}</p>

                    @if($product->isCurrentlyDiscounted())
                        <div>
                            <p class="text-gray-700 lg:text-xl">£{{$product->display_price}} ({{$product->discount_percentage}}%)</p>
                            <p class="text-red-500 text-base line-through">£{{$product->price}}</p>
                        </div>
                    @else
                        <p class="mb-5 lg:mb-0 text lg:text-xl">£{{$product->display_price}}</p>
                    @endif


                    <div>
                        <div class="flex mb-5">
                            <x-stock-pill :product="$product"/>
                        </div>


                        @if($product->isInStock())
                            @if(\App\Support\Cart\CartManager::hasItemInBasket($product))
                                <x-button.anchor class="p-3 mb-5 block border-orange-500 text-orange-500 opacity-80 pointer-events-none" route="">Item Already In Cart</x-button.anchor>
                            @else
                                {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
                                {{Form::open(['url' => route('basket.add-product', $product)])}}
                                {{Form::button('<i class="fas fa-plus"><span class="sr-only">Add to basket icon</span></i> Add to Basket', ['type' =>'submit', 'class'=>'block mb-5 w-full transition-colors duration-200 border rounded-full text-center p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white'])}}
                                {{Form::close()}}
                            @endif
                        @endif
                    </div>
                </div>
            </x-slot>
        </x-card>

    </div>

@endsection
