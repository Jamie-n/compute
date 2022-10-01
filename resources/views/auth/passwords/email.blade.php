@extends('layouts.app')

@section('content')
    <x-card title="Reset Password">
        <x-slot name="body">
            {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
            {{Form::open(['url' => route('password.email')])}}

            <div class="mb-3">
                <x-input.label for="email" required>Email Address</x-input.label>

                <x-input.generic name="email" id="email" :value="old('email')"/>
            </div>

            <x-button.submit type="submit" class="p-3 border-orange-500 text-orange-500 hover:border-orange-600 hover:text-orange-600">Send Password Reset Link</x-button.submit>
            {{Form::close()}}
        </x-slot>
    </x-card>
@endsection
