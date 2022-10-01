<x-modal>
    <x-slot name="title">
        Editing Discount Code: {{$discountCode->code ?? ''}}
    </x-slot>

    <x-slot:body>
        <div class="mb-3">
            <x-input.label required for="code_active_end">Code Start Date</x-input.label>
            <x-input.generic name="code_active_start" type="date"/>
        </div>

        <div class="mb-3">
            <x-input.label required for="code_active_end">Code End Date</x-input.label>
            <x-input.generic name="code_active_end" type="date"/>
        </div>
    </x-slot:body>

    <x-slot:footer>
        <x-button.anchor wire:click.prevent="save" class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3"> Update Discount Code</x-button.anchor>
    </x-slot:footer>
</x-modal>
