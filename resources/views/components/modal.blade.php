<div x-cloak x-data="{hidden: @entangle('hidden')}">
    <div x-show="!hidden" x-trap.noscroll="!hidden" style="transform: translate(-50%,-50%)" class="p-4 lg:p-0 overflow-x-hidden fixed left-1/2 top-1/2 z-50 w-screen h-screen lg:w-1/2 lg:h-auto">
        <div class="relative w-full max-w-4xl" >
            <div class="relative bg-white rounded-lg">
                <div class="flex justify-between items-center p-4 rounded-t border-b">
                    <p class="text-2xl">
                        {{$title}}
                    </p>
                    <button wire:click.prevent="hide" type="button" class="px-3 py-1 hover:bg-gray-200 hover:text-gray-900 rounded-full">
                        <i class="fas fa-times text-lg"></i>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-6 overflow-y-scroll">
                    {{$body}}
                </div>
                @if($footer ?? false)
                    <div class="flex justify-end p-6 rounded-b border-t">
                        {{$footer}}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div x-show="!hidden"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-80"
         x-transition:leave="transition ease-in duration-250"
         x-transition:leave-start="opacity-80"
         x-transition:leave-end="opacity-0 "
         class="bg-zinc-900 w-screen fixed h-screen top-0 left-0 opacity-80">
    </div>
</div>
