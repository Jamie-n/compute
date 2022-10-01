<div>
    <x-modal>
        <x-slot:title>
            {{$this->title}}
        </x-slot:title>

        <x-slot:body>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-7">
                    <x-input.label for="product.name" required>Name</x-input.label>
                    <x-input.generic wire:model="product.name" name="product.name"/>
                </div>

                <div class="mb-7">
                    <x-input.label for="product.slug" required>Slug</x-input.label>
                    <x-input.generic wire:model="product.slug" name="product.slug"/>
                </div>
            </div>

            <div class="mb-7">
                <x-input.label for="product.brand_id" required>Brand</x-input.label>
                <x-input.select :values="$brands" name="product.brand_id" wire:model="product.brand_id"/>
            </div>

            <div class="mb-7">
                <x-input.label for="product.description" required>Description</x-input.label>
                <x-input.text-area wire:model="product.description" name="product.description"/>
            </div>

            <div class="mb-7">
                <legend>Select Categories <span class="required"></span>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        @foreach($categories as $category)
                            <div>
                                {{Form::checkbox('categories[]',$category->id ,null, ['id' => $category->name, 'wire:model' => "categories"])}}
                                {{Form::label($category->name, null , ['class'=>'pl-2'])}}
                            </div>
                        @endforeach
                    </div>
                </legend>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-7">
                <div class="mb-7">
                    <x-input.label for="product.price" required>Price</x-input.label>
                    <x-input.generic type="number" name="product.price"/>
                </div>

                <div class="mb-7">
                    <x-input.label for="product.discount_percentage" required>Discount Percentage</x-input.label>
                    <x-input.generic type="number" name="product.discount_percentage"/>
                </div>

                <div class="mb-7">
                    <x-input.label for="product.stock_quantity" required>Stock Level</x-input.label>
                    <x-input.generic type="number" name="product.stock_quantity"/>
                </div>
            </div>
            <div class="mb-7">
                <x-input.filepond wire:model="file" :initialize-listener="self::INITIALIZE_JS" :destroy-listener="self::DESTROY_JS"/>
            </div>
        </x-slot:body>

        <x-slot:footer>
            <x-button.anchor wire:click.prevent="save" class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3">{{$this->buttonText}}</x-button.anchor>
        </x-slot:footer>
    </x-modal>
</div>
