<x-modal>
    <x-slot:title>
        Pack Order: {{$order->reference_number}}
    </x-slot:title>

    <x-slot:body>
        <table class="table-fixed w-full">
            <thead>
            <th class="text-left py-2">Item</th>
            <th>Quantity</th>
            <th>Quantity Packed</th>
            </thead>
            <tbody>
            @foreach($order->products as $product)
                <tr>
                    <td class="py-2">
                        <a href="{{route('product.show', $product)}}" target="_blank" class="text-orange-400 hover:text-orange-500 hover:underline">{{$product->name}}</a>
                    </td>
                    <td class="text-center">{{$product->pivot->quantity}}</td>
                    <td class="text-center">
                        <div class="mb-3">
                            <x-input.generic class="text-center" name="packing_quantities.{{$product->getRouteKey()}}" type="number" value="0"/>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-slot:body>

    <x-slot:footer>
        <x-button.anchor wire:click.prevent="generateShippingLabel" wire:target="generateShippingLabel" wire:loading.class.remove="hover:bg-orange-500 border-orange-500" wire:loading.class="bg-orange-300 border-orange-300"
                         class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/2">

            <x-livewire.loading target="generateShippingLabel">
                <x-slot:loadingShow>
                    @if($this->canConfirmOrderPacked())
                        Re-Download Shipping Label
                    @else
                        Generate Shipping Label
                    @endif
                </x-slot:loadingShow>
                <x-slot:loadedShow>
                    Generating Shipping Label
                </x-slot:loadedShow>
            </x-livewire.loading>

        </x-button.anchor>

        @if($this->canConfirmOrderPacked())
            <x-button.anchor wire:click.prevent="orderPacked" wire:target="orderPacked" wire:loading.class.remove="hover:bg-orange-500 border-orange-500" wire:loading.class="bg-orange-300 border-orange-300"
                             class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/2">

                <x-livewire.loading target="orderPacked">
                    <x-slot:loadingShow>
                        Confirm Order Packed
                    </x-slot:loadingShow>
                    <x-slot:loadedShow>
                        Updating Order
                    </x-slot:loadedShow>
                </x-livewire.loading>

            </x-button.anchor>
        @endif
    </x-slot:footer>
</x-modal>
