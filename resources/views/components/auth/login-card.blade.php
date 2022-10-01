<x-card title="Sign In">
    <x-slot name="body">
        {{-- Form facade is used to generate HTML forms from parameters, provided by LaravelCollective/HTML package - https://laravelcollective.com/docs/6.x/html --}}
        {{Form::open(['url' => route('login', [$redirect ?? false ? $redirect : ''])])}}

        @include('layouts.partials._login-form')

        <p class="text-black">Forgot your password? <a href="{{route('password.request')}}" class="text-orange-500 hover:text-orange-600 transition duration-150">Reset
                password</a></p>

        <div class="mt-5">
            {{Form::button('<i class="fas fa-sign-in-alt"><span class="sr-only">Sign in icon</span></i> Sign In', ['type' =>'submit', 'class'=>'block w-full transition-colors duration-200 border rounded-full text-center p-3 border-orange-500 text-orange-500 hover:bg-orange-500 hover:text-white'])}}

            <p class="text-black text-center my-3">Or</p>

            <x-card-subsection hr-bottom>
                <div class="mb-5">
                    <x-button.anchor :href="route('socialite.redirect', config('services.google.name'))" class="block p-3 border-gray-500 text-gray-500 hover:bg-gray-500 hover:text-white">
                        <i class="fab fa-google"><span class="sr-only">Google icon</span></i> Sign In With Google
                    </x-button.anchor>
                </div>

                <div class="mb-5">
                    <x-button.anchor :href="route('socialite.redirect', config('services.facebook.name'))" class="block p-3 border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white">
                        <i class="fab fa-facebook"><span class="sr-only">Facebook icon</span></i> Sign In With Facebook
                    </x-button.anchor>
                </div>
            </x-card-subsection>

            <p class="text-black text-center">New Around Here? <a href="{{route('register')}}" class="text-orange-500 hover:text-orange-600 transition duration-150">Join Now</a></p>
        </div>

        {{Form::close()}}
    </x-slot>
</x-card>
