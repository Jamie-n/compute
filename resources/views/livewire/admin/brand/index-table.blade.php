<div>
    <x-card title="Manage Brands">
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
                                class="fas fa-plus"></i> Add New Brand
                        </x-button.anchor>
                    </div>
                </div>
            </x-card-subsection>
        </x-slot:titleSub>

        <x-slot name="body">
            <div class="overflow-x-scroll">
                <table class="table text-center w-full">
                    <thead>
                    <th>Name</th>
                    <th>Slug</th>
                    <th><span class="sr-only">Actions</span></th>
                    </thead>
                    <tbody>
                    @forelse($brands as $brand)
                        <tr>
                            <td>{{$brand->name}}</td>
                            <td>{{$brand->slug}}</td>
                            <td class="py-2">
                                <x-button.anchor
                                    wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\DiscountCode\EditModal::SHOW}}', '{{$brand->id}}')"
                                    class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3">
                                    <i class="far fa-edit"><span class="sr-only">Edit icon</span></i>
                                    Edit
                                </x-button.anchor>
                                <x-button.anchor
                                    wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\Brand\DeleteModal::SHOW}}', '{{$brand->id}}')"
                                    class="inline-block p-3 border-red-500 text-red-500 hover:bg-red-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3">
                                    <i class="fas fa-ban"><span class="sr-only">Cancel icon</span></i> Delete
                                </x-button.anchor>
                            </td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="3">No Brands Found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :models="$brands"/>
        </x-slot>
    </x-card>

    <livewire:admin.brand.delete-modal/>
    <livewire:admin.brand.edit-modal/>
    <livewire:admin.brand.create-modal/>
</div>
