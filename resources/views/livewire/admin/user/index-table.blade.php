<div>
    <x-card title="Manage Administrative Users">
        <x-slot:titleSub>
            <x-card-subsection>
                <div class="grid grid-rows-12 lg:grid-cols-12 gap-5 items-center">
                    <div class="col-span-12 lg:col-span-2 text-center lg:text-left">
                        <x-go-back-link :href="route('admin.index')">Back to Admin</x-go-back-link>
                    </div>

                    <div class="col-span-12 lg:col-start-11 lg:col-span-2">
                        <x-button.anchor wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\User\CreateModal::SHOW}}')"
                                         class="block p-3 border-black text-black hover:bg-black hover:text-white mb-3 lg:mb-0"><i
                                class="fas fa-plus"></i> Add New User
                        </x-button.anchor>
                    </div>
                </div>

            </x-card-subsection>

            <x-card-subsection hr-top>
                <div class="mb-3">
                    <x-input.label for="search_term">Search Users</x-input.label>
                    <x-input.generic type="text" name="search_term"/>
                </div>
            </x-card-subsection>
        </x-slot:titleSub>

        <x-slot name="body">
            <div class="overflow-x-scroll">
                <table class="table text-center w-full">
                    <thead>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Created At</th>
                    <th><span class="sr-only">Actions</span></th>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4">{{$user->name}}</td>
                            <td class="px-4">{{$user->email}}</td>
                            <td class="px-4">{{\Illuminate\Support\Str::of(implode(', ',$user->roles->pluck('name')->toArray()))->replace('_', ' ')->title()}}</td>
                            <td>{{$user->created_at->format('d/m/Y H:i:s')}}</td>
                            <td class="px-4 lg:px-0 py-5 w-1/4">
                                @if($user->is(auth()->user()))
                                    <x-button.anchor title="You cannot edit yourself"
                                                     class="lg:inline-block cursor-not-allowed block p-3 mb-3 border-orange-500 text-orange-500 mb-3 lg:mb-0 w-full lg:w-1/3 opacity-50 disabled">
                                        <i class="far fa-edit"><span class="sr-only">Edit icon</span></i>
                                        Edit
                                    </x-button.anchor>
                                    <x-button.anchor title="You cannot delete yourself"
                                                     class="lg:inline-block cursor-not-allowed block p-3 border-red-500 text-red-500 mb-3 lg:mb-0 w-full lg:w-1/3 opacity-50 disabled">
                                        <i class="fas fa-ban"><span class="sr-only">Cancel icon</span></i> Delete
                                    </x-button.anchor>
                                @else
                                    <x-button.anchor
                                        wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\User\EditModal::SHOW}}', '{{$user->slug}}')"
                                        class="block lg:inline-block p-3 border-orange-500 mb-3 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3">
                                        <i class="far fa-edit"><span class="sr-only">Edit icon</span></i>
                                        Edit
                                    </x-button.anchor>
                                    <x-button.anchor
                                        wire:click.prevent="$emit('{{\App\Http\Livewire\Admin\User\DeleteModal::SHOW}}', '{{$user->slug}}')"
                                        class="block lg:inline-block p-3 border-red-500 text-red-500 hover:bg-red-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3">
                                        <i class="fas fa-ban"><span class="sr-only">Cancel icon</span></i> Delete
                                    </x-button.anchor>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="4">No Users Found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :models="$users"/>
        </x-slot>
    </x-card>

    <livewire:admin.user.edit-modal/>
    <livewire:admin.user.delete-modal/>
    <livewire:admin.user.create-modal/>
</div>
