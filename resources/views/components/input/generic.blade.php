<input {{$attributes->class([
    'rounded p-2 w-full bg-gray-100 border border-1 border-black-200',
  'border-red-500' => $errors->{$messageBag??'default'}->has($attributes->get('wire:model') ?? $name),
])}} wire:model.debounce.250ms="{{$attributes->get('wire:model') ?? $name}}" id="{{$id ?? $name}}">

<x-input.error-message :name="$attributes->get('wire:model') ?? $name" :message-bag="$messageBag ?? 'default'"/>
