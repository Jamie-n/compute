<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    protected $errorBag = 'checkout';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email_address' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'county' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:255']
        ];
    }

    public function authorize(): bool
    {
        return auth()->check();
    }
}
