<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email_address',
        'phone_number',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
    ];


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'delivery_address_id');
    }
}
