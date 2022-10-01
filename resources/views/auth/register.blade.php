@extends('layouts.app')

@section('content')
    <x-card title="Register">
        <x-slot name="body">
            {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
            {{Form::open(['url' => route('register')])}}

            <div class="mb-3">
                <x-input.label for="name" required>Name</x-input.label>

                <x-input.generic type="text" id="name" name="name" :value="old('name')"/>
            </div>

            <div class="mb-3">
                <x-input.label for="email" required>Email Address</x-input.label>

                <x-input.generic type="text" id="email" name="email" :value="old('email')"/>
            </div>

            <div class="mb-3">
                <x-input.label for="password" required>Password</x-input.label>

                <x-input.generic type="password" id="password" name="password"/>
            </div>

            <div class="mb-3">
                <x-input.label for="password_confirmation" required>Password Confirmation</x-input.label>

                <x-input.generic type="password" id="password_confirmation" name="password_confirmation"/>
            </div>

            <x-button.submit type="submit" class="p-3 border-orange-500 text-orange-500 hover:border-orange-600 hover:text-orange-600">Register</x-button.submit>

            {{Form::close()}}

        </x-slot>

    </x-card>
@endsection
