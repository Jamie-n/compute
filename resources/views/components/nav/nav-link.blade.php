<li class="mt-5 lg:mt-0 hover:-translate-y-1 transition ease-in-out">
    <a href="{{$href ?? '#'}}" class="hover:border-b-2 hover:text-orange-500 border-orange-500 pb-1 {{ request()->is($slug) ? 'border-b-2 border-orange-500 text-orange-500' : '' }}">
        {{$title}}
    </a>
</li>
