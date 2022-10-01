<?php

namespace App\Rules;

use App\Models\DiscountCode;
use Illuminate\Contracts\Validation\Rule;

class DiscountCodeValidRule implements Rule
{
    protected ?DiscountCode $code;

    public function __construct(string $code)
    {
        $this->code = DiscountCode::whereCode($code)->first();
    }

    public function passes($attribute, $value): bool
    {
        if (!$this->code)
            return false;

        if (!now()->between($this->code->code_active_start, $this->code->code_active_end))
            return false;

        return true;
    }

    public function message(): string
    {
        return 'The code applied is not valid.';
    }
}
