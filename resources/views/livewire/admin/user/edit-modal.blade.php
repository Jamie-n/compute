<x-modal>
    <x-slot name="title">
        Editing User: {{$user->name ?? ''}}
    </x-slot>

    <x-slot:body>
        <legend>Update User Role <span class="required"></span>
            @foreach($roles as $role)
                <div class="my-2">
                    {{Form::checkbox($role->name,null, $user->hasRole($role->name) , ['id' => $role->name, 'wire:model' => "selectedRoles.{$role->id}"])}}
                    {{Form::label($role->name, null , ['class'=>'pl-2'])}}
                </div>
            @endforeach
        </legend>
    </x-slot:body>

    <x-slot:footer>
        <x-button.anchor wire:click.prevent="save" class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3"> Update User</x-button.anchor>
    </x-slot:footer>
</x-modal>
