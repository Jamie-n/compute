@if(session()->has($key))
    <div {{$attributes->merge(['class'=>'rounded px-3 py-2 my-2'])}}>
        {{session()->get($key)}}
    </div>
@endif

