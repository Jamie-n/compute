<div class="relative">
    <img src="{{asset('images/hero_banner.jpg')}}" alt="AMD Ryzen 7000 Banner" class="w-full h-80 lg:h-auto object-cover">

    <div class="mx-5 container md:mx-auto w-full">
        <div class="absolute text-white top-1/2 -translate-y-1/2">
            <div class="flex flex-col content-center">
                <div class="lg:pr-72">
                    <h2 class="font-bold text-2xl lg:text-5xl mb-2 lg:mb-3">New: AMD Ryzen 7000</h2>
                    <p class="text lg:text-xl">In Stock Now.</p>
                    <x-button.anchor :route="route('storefront.show', '/components')" class="inline-block mt-5 lg:mt-10 p-3 w-auto border-orange-400 text-orange-400 hover:border-orange-500 hover:text-orange-500">Shop Components Now</x-button.anchor>
                </div>
            </div>
        </div>
    </div>
</div>



