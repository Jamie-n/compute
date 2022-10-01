<div>
    <x-modal>
        <x-slot:title>
            Create New Admin
        </x-slot:title>

        <x-slot:body>
            <div class="mb-3">
                <x-input.label for="user.name" required>Name</x-input.label>

                <x-input.generic type="text" name="user.name" id="user.name" autocomplete="false"/>
            </div>
            <div class="mb-3">
                <x-input.label for="email_address" required>Email Address</x-input.label>

                <x-input.generic type="text" name="user.email" wire:model="user.email" autocomplete="false"/>
            </div>
            <div class="mb-3">
                <legend>Select Role <span class="required"></span>
                    @foreach($roles as $role)
                        <div class="my-2">
                            {{Form::checkbox($role->name,null, $user->hasRole($role->name) , ['id' => $role->name, 'wire:model' => "selected_roles.{$role->id}"])}}
                            {{Form::label($role->name, null , ['class'=>'pl-2'])}}
                        </div>
                    @endforeach
                    <x-input.error-message name="selected_roles"/>

                </legend>
            </div>
            <div class="mb-3">
                <x-input.label for="password" required>Password</x-input.label>

                <x-input.generic type="password" name="password" id="password" autocomplete="false"/>
            </div>
            <div class="mb-3">
                <x-input.label for="password_confirmation" required>Password Confirmation</x-input.label>

                <x-input.generic type="password" name="password_confirmation" id="password_confirmation"/>
            </div>


        </x-slot:body>

        <x-slot:footer>
            <x-button.anchor wire:click.prevent="save" class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3"> Create User</x-button.anchor>
        </x-slot:footer>
    </x-modal>
</div>
