<div {{$attributes->merge(['class' => 'rounded overflow-hidden shadow-lg bg-white flex flex-col p-3'])}}>
    @isset($title)
        <span class="text-2xl text-center lg:text-left"> {{ $title }}</span>
        @isset($titleSub)
            <p class="text-gray-700 mt-2">{{$titleSub}}</p>
        @endisset
        <hr class="my-3"/>
    @endisset
    {{$body}}
</div>
