<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class DeliveryType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price'
    ];

    public function getDisplayTextAttribute()
    {
        return $this->name . '. £' . $this->price;
    }

    public static function getAdminDropdownOptions(): Collection
    {
        return DeliveryType::withTrashed()->get()->mapWithKeys(function (DeliveryType $deliveryType) {
            $name = $deliveryType->name;
            $name .= $deliveryType->trashed() ? ' (Inactive)' : '';

            return [$deliveryType->id => $name];

        });
    }
}
