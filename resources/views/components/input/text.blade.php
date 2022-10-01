{{--Form facade is used to generate HTML inputs from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
{{Form::text($name, $value ?? null, [
    'class' => 'rounded p-2 w-full bg-gray-100 border border-1 border-black-200 '. ($errors->{$messageBag ?? 'default'}->has($name) ? 'border-red-500' : ''),
    'id' => $id ?? $name,
    'wire:model' => $attributes->get('wire:model') ?? $name,
    ])}}

<x-input.error-message :name="$name" :message-bag="$messageBag ?? 'default'"/>

