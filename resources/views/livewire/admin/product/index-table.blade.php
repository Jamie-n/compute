<div>
    <x-card title="Manage Products">
        <x-slot:titleSub>
            <x-card-subsection>
                <div class="grid grid-rows-12 lg:grid-cols-12 gap-5 items-center">
                    <div class="col-span-12 lg:col-span-2 text-center lg:text-left">
                        <x-go-back-link :href="route('admin.index')">Back to Admin</x-go-back-link>
                    </div>

                    <div class="col-span-12 lg:col-start-11 lg:col-span-2">
                        <x-button.anchor
                            wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\Product\CreateEditModal::SHOW_CREATE}}')"
                            class="block p-3 border-black text-black hover:bg-black hover:text-white lg:mb-0"><i
                                class="fas fa-plus"></i> Add New Product
                        </x-button.anchor>
                    </div>
                </div>
            </x-card-subsection>

            <x-card-subsection hr-top>
                <p class="text-xl mb-3">Filter Products</p>

                <div class="mb-3">
                    <x-input.label for="search_term">Search Products</x-input.label>
                    <x-input.generic type="text" name="search_term"/>
                </div>

            </x-card-subsection>

        </x-slot:titleSub>

        <x-slot name="body">
            <div class="overflow-x-scroll">
                <table class="table text-center w-full">
                    <thead>
                    <th>Name</th>
                    <th>Unit Price</th>
                    <th>Display Price</th>
                    <th>Stock</th>
                    <th><span class="sr-only">Actions</span></th>
                    </thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="p-4">{{$product->name}}</td>
                            <td class="p-4">£{{$product->price}}</td>
                            <td class="p-4">£{{$product->display_price}}
                                @if($product->isCurrentlyDiscounted())
                                    ({{$product->discount_percentage}}%)
                                @endif</td>
                            <td class="p-4">{{$product->stock_quantity}}</td>
                            <td>
                                <x-button.anchor
                                    wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\Product\CreateEditModal::SHOW_EDIT}}', '{{$product->slug}}')"
                                    class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3">
                                    <i class="far fa-edit"><span class="sr-only">Edit icon</span></i> Edit
                                </x-button.anchor>

                                <x-button.anchor
                                    wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\Product\DeleteModal::SHOW}}', '{{$product->slug}}')"
                                    class="inline-block p-3 border-red-500 text-red-500 hover:bg-red-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3">
                                    <i class="fas fa-ban"><span class="sr-only">Cancel icon</span></i> Delete
                                </x-button.anchor>

                            </td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="5">No Products Found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :models="$products"/>
        </x-slot>
    </x-card>

    <livewire:admin.product.create-edit-modal/>
    <livewire:admin.product.delete-modal/>
</div>
