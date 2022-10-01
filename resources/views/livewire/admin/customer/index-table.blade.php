<div>
    <x-card title="View Registered Customers">
        <x-slot:titleSub>

            <x-card-subsection>
                    <div class="text-center lg:text-left">
                        <x-go-back-link :href="route('admin.index')">Back to Admin</x-go-back-link>
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
                    <th>Account Type</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created At</th>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4 lg:px-0 py-5">
                                @if($user->isOauthUser())
                                    <div class="lg:w-1/3 inline-block text-center border p-2 border-orange-500 rounded-full" title="oAuth Account">
                                        <i class="fab fa-facebook text-orange-500"></i> <span class="text-orange-500">|</span> <i class="fab fa-google text-orange-500"></i>
                                    </div>
                                @else
                                    <div class="w-1/3 inline lg:inline-block text-center border p-2 border-orange-500 rounded-full" title="Local Account">
                                        <i class="fas fa-microchip text-orange-500"></i>
                                    </div>

                                @endif
                            </td>
                            <td class="px-4">{{$user->name}}</td>
                            <td class="px-4">{{$user->email}}</td>
                            <td class="px-4">{{$user->created_at->format('d/m/Y H:i:s')}}</td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="4">No Users Found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :models="$users"/>
        </x-slot>
    </x-card>
</div>
