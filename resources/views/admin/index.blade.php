@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    @if(auth()->user()->hasPermissionTo(\App\Support\Enums\Permissions::MANAGE_USERS->value))
        <x-card title="User Management" class="mb-5">
            <x-slot name="body">
                <x-button.anchor
                    class="p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                    :route="route('admin.users.admins.index')"><i class="fa-solid fa-users"><span
                            class="sr-only">user icon</span></i> Manage Administrators
                </x-button.anchor>

                <x-button.anchor
                    class="p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                    :route="route('admin.users.customers.index')"><i class="fa-solid fa-user"><span
                            class="sr-only">user icon</span></i> View Customers
                </x-button.anchor>
            </x-slot>
        </x-card>
    @endif

    @if(auth()->user()->hasPermissionTo(\App\Support\Enums\Permissions::MANAGE_PRODUCTS->value))
        <x-card title="Product Management" class="mb-5">
            <x-slot:body>
                <x-button.anchor class="block p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                                 :route="route('admin.products.index')"><i class="fas fa-box"><span
                            class="sr-only">Manage Products</span></i>
                    Manage Products
                </x-button.anchor>

                <x-button.anchor class="block p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                                 :route="route('admin.brand.index')"><i class="fas fa-copyright"><span
                            class="sr-only">Manage Brands</span></i>
                    Manage Brands
                </x-button.anchor>

                <x-button.anchor class="block p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                                 :route="route('admin.discount-codes.index')"><i class="fas fa-tag"><span
                            class="sr-only">Manage Discount Codes</span></i>
                    Manage Discount Codes
                </x-button.anchor>
            </x-slot:body>
        </x-card>
    @endif

    @if(auth()->user()->hasPermissionTo(\App\Support\Enums\Permissions::MANAGE_SHIPPING->value))
        <x-card title="Stock/Warehouse Management" class="mb-5">
            <x-slot:body>
                <x-button.anchor class="p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                                 :route="route('admin.shipping.index')"><i class="fas fa-boxes"><span class="sr-only">Manage Stock</span></i>
                    Manage Shipping
                </x-button.anchor>

                <x-button.anchor class="p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                                 :route="route('admin.delivery.index')"><i class="fas fa-shipping-fast"><span class="sr-only">Manage Delivery Options</span></i>
                    Manage Delivery Options
                </x-button.anchor>
            </x-slot:body>
        </x-card>
    @endif

@endsection
