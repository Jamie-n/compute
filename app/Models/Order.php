<?php

namespace App\Models;

use App\Support\States\OrderStatus;
use App\Support\States\Processing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\ModelStates\HasStates;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasStates;

    protected $fillable = [
        'reference_number',
        'name',
        'delivery_type_id',
        'additional_delivery_info',
        'status'
    ];

    protected $casts = [
        'status' => OrderStatus::class
    ];

    public function getRouteKeyName(): string
    {
        return 'reference_number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryType(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'delivery_address_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
            ->withTrashed()
            ->withPivot('quantity', 'unit_price');
    }

    public function canEditOrder(): bool
    {
        return $this->status->getName() == Processing::getName();
    }

    public function totalCost(): float
    {
        return $this->products->sum(function (Product $product) {
            return $product->pivot->unit_price * $product->pivot->quantity;
        });
    }

    /**
     * When deleting models we increase the stock available based on the quantity ordered
     * @return void
     */
    public function delete(): void
    {
        $this->products()->each(function (Product $product) {
            $orderQuantity = $product->pivot->quantity;

            $product->increaseStock($orderQuantity);
        });

        parent::delete();
    }
}
