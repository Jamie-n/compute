@extends('layouts.app')

@section('content')

    <x-card title="Reset Password">
        <x-slot name="body">
            {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
            {{Form::open(['url' => route('password.update')])}}

            {{Form::hidden('token', $token)}}

            <div class="mb-3">
                <x-input.label for="email" required>Email Address</x-input.label>

                <x-input.generic type="text" id="email" name="email" :value="old('email')"/>
            </div>

            <div class="mb-3">
                <x-input.label for="password" required>Password</x-input.label>

                <x-input.generic type="password" id="password" name="password"/>
            </div>

            <div class="mb-3">
                <x-input.label for="password_confirmation" required>Confirm Password</x-input.label>

                <x-input.generic type="password" id="password_confirmation" name="password_confirmation"/>
            </div>

            <x-button.submit type="submit" class="p-3 border-orange-500 text-orange-500 hover:border-orange-600 hover:text-orange-600">Reset Password</x-button.submit>
        </x-slot>
    </x-card>
@endsection
