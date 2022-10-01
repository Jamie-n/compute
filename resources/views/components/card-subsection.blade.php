@isset($hrTop)
    <hr class="my-3">
@endisset
<div {{$attributes->merge(['class'=> 'my-3'])}}>
    {{$slot}}
</div>
@isset($hrBottom)
    <hr class="my-3">
@endisset
