<div>
<x-modal>
    <x-slot:title>
        Delete User: {{$user->name}}
    </x-slot:title>

    <x-slot:body>
        <p>Are you sure that you want to delete this user, this action cannot be undone.</p>
    </x-slot:body>

    <x-slot:footer>
        <x-button.anchor wire:click="delete" class="inline-block p-3 border-red-500 text-red-500 hover:bg-red-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3"> Delete User</x-button.anchor>
    </x-slot:footer>
</x-modal>
</div>
