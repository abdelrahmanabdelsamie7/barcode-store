<?php
namespace App\Models;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\{SubCategory, Brand, ProductColor, Color, ProductColorImage, ProductVariant, Size, Offer};

class Product extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'products';
    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_cover',
        'sku',
        'price_before_discount',
        'status',
        'sub_category_id',
        'brand_id',
    ];
    public function offers()
    {
        return $this->morphMany(Offer::class, 'offerable');
    }
    public function getDiscountAttribute()
    {
        $now = now();
        $subCatOffers = $this->sub_category?->offers()
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->get();
        $subCatDiscount = $subCatOffers->isNotEmpty()
            ? $subCatOffers->max('discount')
            : 0;
        $productOffer = $this->offers()
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->latest()
            ->first();
        $productDiscount = $productOffer ? $productOffer->discount : 0;
        return max($subCatDiscount, $productDiscount);
    }
    public function getFinalPriceAttribute()
    {
        $discount = $this->discount ?? 0;
        return $this->price_before_discount * (1 - $discount / 100);
    }
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function product_colors()
    {
        return $this->hasMany(ProductColor::class, 'product_id');
    }
    public function colors()
    {
        return $this->hasManyThrough(Color::class, ProductColor::class, 'product_id', 'id', 'id', 'color_id');
    }
    public function product_color_images()
    {
        return $this->hasManyThrough(
            ProductColorImage::class,
            ProductColor::class,
            'product_id',
            'product_color_id',
            'id',
            'id'
        );
    }
    public function product_variants()
    {
        return $this->hasManyThrough(
            ProductVariant::class,
            ProductColor::class,
            'product_id',
            'product_color_id',
            'id',
            'id'
        );
    }
    public function sizes()
    {
        return $this->hasManyDeep(
            Size::class,
            [ProductColor::class, ProductVariant::class],
            ['product_id', 'product_color_id'],
            ['id', 'id']
        );
    }
}
