<div class="bg-gray-800 text-center lg:text-none">
    <div class="flex flex-col lg:flex-row justify-between mx-auto container py-10 px-5 lg:px-0" role="banner">

        <h1 class="text-4xl text-white mb-5 lg:mb-0"><a href="{{route('storefront.index')}}">{{config('app.name')}}</a></h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <x-button.anchor
                class="{{request()->is('basket')? 'border-white text-white' : 'hover:border-white hover:text-white border-neutral-400 text-neutral-400'}} inline-block p-3 order-last lg:order-first"
                :route="route('basket.index')">
                <i class="fas fa-shopping-basket"><span class="sr-only">Basket icon</span></i>
                Basket @if(CartManager::hasItemsInBasket())
                    ({{CartManager::itemCountInBasket()}})
                @endif
            </x-button.anchor>

            @auth()
                <x-button.anchor
                    class="{{request()->is('user/*')? 'border-orange-500 text-orange-500' : 'hover:border-orange-500 hover:text-orange-500 border-orange-400 text-orange-400'}} inline-block p-3"
                    :route="route('user.show', auth()->user())">
                    <i class="far fa-user-circle"><span class="sr-only">Account icon</span></i> My Account
                </x-button.anchor>
            @else
                <x-button.anchor
                    class="{{request()->is('login')? 'border-orange-500 text-orange-500' : 'hover:border-orange-500 hover:text-orange-500 border-orange-400 text-orange-400'}} inline-block p-3"
                    :route="route('login')">
                    <i class="fas fa-sign-in"><span class="sr-only">Sign in icon</span></i> Sign In
                </x-button.anchor>
            @endauth
        </div>
    </div>
</div>
