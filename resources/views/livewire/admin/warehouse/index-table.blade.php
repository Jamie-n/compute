<div>
    <x-card title="Manage Orders">
        <x-slot:titleSub>
            <x-card-subsection>
                <div class="text-center lg:text-left">
                    <x-go-back-link :href="route('admin.index')">Back to Admin</x-go-back-link>
                </div>
            </x-card-subsection>

            <x-card-subsection hr-top>
                <p class="text-xl mb-3">Filter Orders</p>
                <div class="mb-3">
                    <x-input.label for="delivery_type" name="delivery_type">Delivery Type</x-input.label>
                    <x-input.select name="delivery_type" id="delivery_type" :values="$deliveryTypes"/>
                </div>
                <div class="mb-3">
                    <x-input.label for="order_status" name="order_status">Order Status</x-input.label>
                    <x-input.select name="order_status" id="order_status" :values="$orderStatuses"/>
                </div>
                <div class="mb-3">
                    <x-input.label for="reference_number" name="reference_number">Reference Number</x-input.label>
                    <x-input.generic name="reference_number" id="reference_number"/>
                </div>

            </x-card-subsection>
        </x-slot:titleSub>

        <x-slot name="body">
            <div class="overflow-x-scroll">
                <table class="table text-center w-full">
                    <thead>
                    <th>Reference Number</th>
                    <th>Placed At</th>
                    <th>Status</th>
                    <th><span class="sr-only">Actions</span></th>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="p-4"><p class="text-orange-400 hover:text-orange-500"> {{$order->reference_number}}</p>
                            </td>
                            <td class="text-center p-4">{{$order->created_at->format('d/m/y H:i:s')}}</td>
                            <td class="text-center p-4">{{\Illuminate\Support\Str::title($order->status->getName())}}</td>
                            <td class="p-4">
                                @if($order->status == \App\Support\States\Packing::class)
                                    <x-button.anchor
                                        wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\Warehouse\PackOrderModal::SHOW}}','{{$order->getRouteKey()}}')"
                                        class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full">
                                        Pack Order
                                    </x-button.anchor>
                                @else
                                    <x-button.anchor
                                        class="inline-block p-3 border-orange-400 text-orange-400 mb-3 lg:mb-0 w-full cursor-not-allowed ">
                                        Order {{$order->status->getName()}}
                                    </x-button.anchor>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="4">No Orders Found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :models="$orders"/>
        </x-slot>
    </x-card>

    <livewire:admin.warehouse.pack-order-modal/>

</div>
