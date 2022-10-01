<a @if($route ?? false) href="{{$route}}" @endif {{$attributes->merge([
    'class' => "transition-colors duration-200 border rounded-full text-center cursor-pointer"
    ])}}>
    {{$slot}}
</a>
