<a href="{{route('product.show', $product)}}" class="rounded overflow-hidden flex flex-col p-3 bg-white hover:shadow-xl transition inline text-center">

    <img src="{{ $product->getProductImageUrl() }}" alt="{{$product->name}}" class="py-12 w-full"/>
    <div class="mb-4 justify-end">
        <p class="line-clamp-1 mb-5">{{$product->name}}</p>
    </div>
    <div class="mt-auto">
        @if($product->isCurrentlyDiscounted())
            <p class="text-red-500 text-sm line-through">£{{$product->price}}</p>
            <p class="text-gray-700 text-base">£{{$product->display_price}} ({{$product->discount_percentage}}%)</p>

        @else
            <p class="text-gray-700 text-base">£{{$product->display_price}}</p>
        @endif
        <div class="mt-4">
            <x-stock-pill :product="$product"/>
        </div>
    </div>
</a>
