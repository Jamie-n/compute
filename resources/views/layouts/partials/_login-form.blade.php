<div class="mb-3">
    <x-input.label for="email" required>Email</x-input.label>

    <x-input.generic type="text" id="email" name="email" :value="old('email')"/>
</div>

<div class="mb-3">
    <x-input.label for="password" required>Password</x-input.label>

    <x-input.generic type="password" id="password" name="password"/>
</div>

