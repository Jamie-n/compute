<a {{$attributes->merge(['class'=> 'hover:text-orange-500 hover:border-b-2 border-orange-500' ])}}>
    <i class="fa-solid fa-arrow-left"></i> {{$slot ?? 'Go Back'}}
</a>


