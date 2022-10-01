<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'discount_percentage',
        'code_active_start',
        'code_active_end',
    ];

    protected $casts = [
        'code_active_start' => 'datetime',
        'code_active_end' => 'datetime'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'discount_code_id');
    }
}
