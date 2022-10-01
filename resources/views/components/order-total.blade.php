<div>
    @isset($discount)
        <p class="text-xl">Order Total: £{{$discount}}</p>
        <p class="text line-through text-red-500">Order Total: £{{$total}}</p>
    @else
        <p class="text-xl">Order Total: £{{$total}}</p>
    @endif
    <p class="mt-3 text-sm text-gray-700">All Prices Include VAT.</p>
</div>
