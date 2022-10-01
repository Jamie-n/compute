<?php

namespace App\Http\Livewire\Traits;

use App\Models\DiscountCode;
use Carbon\Carbon;

trait UpdatesDiscountCodeStartEndDate
{
    public function updateStartEndDate(DiscountCode $discountCode, string $start, string $end)
    {
        $discountCode->code_active_start = Carbon::createFromFormat('Y-m-d', $start);
        $discountCode->code_active_end = Carbon::createFromFormat('Y-m-d', $end);
    }
}
