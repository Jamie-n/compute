@extends('layouts.app')

@section('content')
    <x-card title="Welcome, {{$account->name}}">
        <x-slot name="body">

            @can(\App\Support\Enums\Permissions::ADMIN->value)
                <x-button.anchor
                    class="p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                    :route="route('admin.index')"><i class="fa-solid fa-users-gear"><span
                            class="sr-only">admin icon</span></i> System Admin
                </x-button.anchor>
            @endcan

                <x-button.anchor class="p-3 my-3 border-black text-black hover:bg-black hover:text-white"
                                 :route="route('order.index')"><i class="fas fa-shipping-fast"><span class="sr-only">My orders icon</span></i>
                    My Orders
                </x-button.anchor>

                {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
                {{Form::open(['url' => route('logout')])}}
                <x-button.submit
                    class="w-full p-3 my-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white"
                    type="submit"><i class="fas fa-sign-out-alt"><span class="sr-only">Sign out icon</span></i> Sign Out
                </x-button.submit>
                {{Form::close()}}
        </x-slot>
    </x-card>

@endsection
