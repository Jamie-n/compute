<textarea {{$attributes->class([
    'rounded p-2 w-full bg-gray-100 border border-1 border-black-200',
  'border-red-500' => $errors->{$messageBag??'default'}->has($name),
])}} wire:model="{{$attributes->get('wire:model') ?? $name }}"></textarea>

<x-input.error-message :name="$name" :message-bag="$messageBag ?? 'default'"/>
