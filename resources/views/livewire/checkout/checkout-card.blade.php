<div x-data="{edit: @entangle('editShipping')}">
    <div x-show="edit" x-transition.duration>
        <x-error-summary bag-name="checkout" summary-message="Unfortunately we could not process your order." post-summary-message="To Proceed with your purchase please resolve these errors."/>

        <div class="mb-3">
            <x-input.label for="name" required>Name</x-input.label>

            <x-input.text name="shipping_info.name" id="name"/>
        </div>

        <div class="mb-3">
            <x-input.label for="email_address" required>Email Address</x-input.label>

            <x-input.text name="shipping_info.email_address" id="email_address"/>
        </div>
        <div class="mb-3">
            <x-input.label for="phone_number" required>Phone Number</x-input.label>

            <x-input.text name="shipping_info.phone_number" id="phone_number"/>
        </div>
        <div class="mb-3">
            <x-input.label for="address_line_1" required>Address Line 1</x-input.label>

            <x-input.text name="shipping_info.address_line_1" id="address_line_1"/>
        </div>
        <div class="mb-3">
            <x-input.label for="address_line_2">Address Line 2</x-input.label>

            <x-input.text name="shipping_info.address_line_2" id="address_line_2"/>
        </div>
        <div class="mb-3">
            <x-input.label for="city" required>City</x-input.label>

            <x-input.text name="shipping_info.city" id="city"/>
        </div>
        <div class="mb-3">
            <x-input.label for="county" required>County</x-input.label>

            <x-input.text name="shipping_info.county" id="county"/>
        </div>
        <div class="mb-3">
            <x-input.label for="postcode" required>Postcode</x-input.label>

            <x-input.text name="shipping_info.postcode" id="postcode"/>
        </div>

        <div class="my-10">
            <x-button.anchor class="p-3 hover:border-orange-500 hover:text-orange-500 border-orange-400 text-orange-400 w-full inline-block" wire:click.prevent="validateShipping">Save Shipping Information</x-button.anchor>
        </div>
    </div>
    <div x-show="!edit" x-transition.duration.250ms>
        <p class="text-xl mb-3">Delivery Address</p>
        <x-card-subsection hr-top>
            <div>
                <p class="mb-3"><span class="font-bold">Name: </span>{{$shipping_info->get('name')}}</p>
                <p class="mb-3"><span class="font-bold">Address Line 1: </span>{{$shipping_info->get('address_line_1')}}</p>
                @if($shipping_info->has('address_line_2'))
                    <p class="mb-3"><span class="font-bold">Address Line 2: </span>{{$shipping_info->get('address_line_2')}}</p>
                @endif
                <p class="mb-3"><span class="font-bold">Town/City: </span>{{$shipping_info->get('city')}}</p>
                <p class="mb-3"><span class="font-bold">County: </span>{{$shipping_info->get('county')}}</p>
                <p class="mb-3"><span class="font-bold">Post Code: </span>{{$shipping_info->get('postcode')}}</p>
            </div>
            <x-button.anchor class="p-3 hover:border-orange-500 hover:text-orange-500 border-orange-400 text-orange-400 inline-block w-full" wire:click.prevent="editShipping">Edit Shipping Information</x-button.anchor>
        </x-card-subsection>

        <x-card-subsection hr-top>
            <div class="mb-3">
                <x-input.label for="delivery_info">Delivery Type</x-input.label>
                <x-input.select :values="$deliveryTypes" name="delivery_type_id" id="delivery_info"/>
            </div>

            <div class="mb-3">
                <x-input.label for="delivery_info">Additional Delivery Information</x-input.label>
                <x-input.text-area name="additional_delivery_info" id="delivery_info"/>
            </div>
        </x-card-subsection>

        <x-card-subsection hr-top>
            <div class="mb-3">
                <x-input.label for="discount_code">Discount Code</x-input.label>
                <x-input.generic name="discount_code" id="discount_code"></x-input.generic>
            </div>

            <x-button.anchor class="p-3 hover:border-orange-500 hover:text-orange-500 border-orange-400 text-orange-400 inline-block w-full" wire:click.prevent="applyDiscountCode">Apply Discount Code</x-button.anchor>
        </x-card-subsection>

        <x-card-subsection hr-top>
            @if($this->discountCode)
                <x-order-total :discount="$this->getBasketTotal()" :total="$this->getOriginalTotal()"/>
            @else
                <x-order-total :total="$this->getBasketTotal()"/>
            @endif
        </x-card-subsection>

        <x-card-subsection hr-top wire:ignore>
            <x-paypal/>
        </x-card-subsection>
    </div>

</div>
