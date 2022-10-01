<?php

namespace App\Models;

use App\Exceptions\StockQuantityException;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'price',
        'discount_percentage',
        'stock_quantity',
        'image'
    ];

    protected $attributes = [
        'discount_percentage' => 0
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class)->withTrashed();
    }

    public function scopeByCategory(Builder $builder, Category $category): Builder
    {
        return $builder->when($category, function (Builder $builder) use ($category) {
            $builder->whereHas('categories', function (Builder $q) use ($category) {
                $q->where('category_id', '=', $category->id);
            });
        });
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getPriceAttribute($value): string
    {
        if (!is_float($value))
            return $value ?? 0;

        return number_format($value, 2, '.', '');
    }

    public function getDisplayPriceAttribute(): string
    {
        if (!$this->discount_percentage)
            return $this->price;

        return number_format($this->price - (($this->discount_percentage / 100) * $this->price), 2, '.', '');
    }

    public function isCurrentlyDiscounted()
    {
        return $this->discount_percentage;
    }

    /**
     * @throws StockQuantityException
     */
    public function reduceStock(int $quantity)
    {
        $stockQuantity = $this->refresh()->stock_quantity -= $quantity;

        if ($stockQuantity < 0)
            throw new StockQuantityException('This item is out of stock');

        $this->save();
    }

    public function increaseStock(int $quantity)
    {
        $this->increment('stock_quantity', $quantity);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id');
    }

    public function getProductImageUrl(): string
    {
        if (is_null($this->image)) {
            return Storage::disk('public')->url('placeholder_img.png');
        }

        return Storage::disk(config('filesystems.default_disk.product.storage'))->url($this->image);
    }

    public static function generateProductImageName($fileName): string
    {
        $timestamp = Date::now()->format('d_m_y_h_i_s');

        return "{$fileName}_{$timestamp}";
    }

    public function scopeFilter(Builder $builder, string $searchTerm)
    {
        return $builder->when($searchTerm, function (Builder $builder) use ($searchTerm) {
            return $builder->where(function (Builder $builder) use ($searchTerm) {
                $builder->where('name', 'LIKE', "%$searchTerm%");
            });
        });
    }
}
