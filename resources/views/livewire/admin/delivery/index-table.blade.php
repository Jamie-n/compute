<div>
    <x-card title="Manage Delivery Options">
        <x-slot:titleSub>
            <x-card-subsection>
                <div class="grid grid-rows-12 lg:grid-cols-12 gap-5 items-center">
                    <div class="col-span-12 lg:col-span-2 text-center lg:text-left">
                        <x-go-back-link :href="route('admin.index')">Back to Admin</x-go-back-link>
                    </div>

                    <div class="col-span-12 lg:col-start-11 lg:col-span-2">
                        <x-button.anchor
                            wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\Delivery\CreateModal::SHOW}}')"
                            class="block p-3 border-black text-black hover:bg-black hover:text-white lg:mb-0"><i
                                class="fas fa-plus"></i> Add New Delivery Option
                        </x-button.anchor>
                    </div>
                </div>
            </x-card-subsection>
        </x-slot:titleSub>

        <x-slot name="body">
            <div class="overflow-x-scroll">
                <table class="table text-center w-full">
                    <thead>
                    <th class="p-4">Name</th>
                    <th class="p-4">Description</th>
                    <th class="p-4">Delivery Price</th>
                    <th class="p-4"><span class="sr-only">Actions</span></th>
                    </thead>
                    <tbody>
                    @forelse($deliveryTypes as $deliveryType)
                        <tr>
                            <td class="p-4">{{$deliveryType->name}}</td>
                            <td class="p-4">{{$deliveryType->description ?? '-'}}</td>
                            <td class="p-4">£{{$deliveryType->price}}</td>
                            <td class="p-4">
                                <x-button.anchor
                                    wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\Delivery\DeleteModal::SHOW}}', '{{$deliveryType->id}}')"
                                    class="inline-block p-3 border-red-500 text-red-500 hover:bg-red-500 hover:text-white mb-3 lg:mb-0 w-full">
                                    <i class="fas fa-ban"><span class="sr-only">Cancel icon</span></i> Delete
                                </x-button.anchor>
                            </td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="4">No Delivery Types Found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :models="$deliveryTypes"/>
        </x-slot>
    </x-card>

    <livewire:admin.delivery.delete-modal/>
    <livewire:admin.delivery.create-modal/>
</div>
