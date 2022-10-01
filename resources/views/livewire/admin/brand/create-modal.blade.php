<x-modal>
    <x-slot name="title">
       Create New Brand
    </x-slot>

    <x-slot:body>
        <div class="mb-3">
            <x-input.label required for="name">Name</x-input.label>
            <x-input.generic name="brand.name" id="name"/>
        </div>

        <div class="mb-3">
            <x-input.label required for="slug">Slug</x-input.label>
            <x-input.generic name="brand.slug" id="slug"/>
        </div>
    </x-slot:body>

    <x-slot:footer>
        <x-button.anchor wire:click.prevent="save" class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3"> Create Brand</x-button.anchor>
    </x-slot:footer>
</x-modal>
