@extends('layouts.app')

@section('content')
    <x-card title="My Orders">
        <x-slot name="body">
            <table class="table-fixed w-full mb-5">
                <thead>
                <th class="text-left py-2">Order Reference</th>
                <th>Date</th>
                <th>Status</th>
                <th><span class="sr-only">Action</span></th>
                </thead>

                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="py-5"><a href="{{route('order.show', $order)}}" class="text-orange-400 hover:text-orange-500 hover:underline"> {{$order->reference_number}}</a></td>
                        <td class="text-center">{{$order->created_at->format('d/m/y H:i:s')}}</td>
                        <td class="text-center">{{\Illuminate\Support\Str::title($order->status->getName())}}</td>
                        <td>
                            <div class="text-center">
                                @can('update', $order)
                                    <x-button.anchor :route="route('order.edit',$order)" class="inline-block p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white mb-3 lg:mb-0 w-full lg:w-1/3"><i class="far fa-edit"><span class="sr-only">Edit icon</span></i> Edit
                                    </x-button.anchor>
                                @endcan
                                @can('delete', $order)
                                    {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
                                    {{Form::open(['url' => route('order.destroy', $order),'method' => 'DELETE', 'class'=> 'inline'])}}
                                    <x-button.submit type="submit" class="p-3 border-red-500 text-red-500 hover:bg-red-500 hover:text-white w-full lg:w-1/3"><i class="fas fa-ban"><span class="sr-only">Cancel icon</span></i> Cancel</x-button.submit>
                                    {{Form::close()}}
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            No Orders Found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <x-pagination :models="$orders"/>
        </x-slot>
    </x-card>
@endsection
