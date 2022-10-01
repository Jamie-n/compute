<div class="bg-white shadow-lg shadow-slate-200">
    <nav class="container mx-auto">
        <ul class="flex flex-col lg:flex-row flex-wrap justify-between items-center text-lg py-3">
            @foreach($categories as $category)
                <x-nav.nav-link :title="$category->name" :href="route('storefront.show', $category)"
                                :slug="$category->slug"/>
            @endforeach
        </ul>
    </nav>
</div>
