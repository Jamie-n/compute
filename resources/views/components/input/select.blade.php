{{Form::select($name, $values, $selected ?? null,
[
    'class' =>  'rounded p-2 w-full bg-gray-100 border border-1 border-black-200'. ($errors->{$messageBag??'default'}->has($name) ? 'border-red-500' : ''),
     'wire:model' =>  $attributes->get('wire:model') ?? $name,
     'id' => $id ?? $name
     ])}}

<x-input.error-message :name="$name" :message-bag="$messageBag ?? 'default'"/>
