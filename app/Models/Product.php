<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\{SubCategory, Brand, ProductColor, Color, ProductColorImage, ProductVariant};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'discount',
        'status',
        'sub_category_id',
        'brand_id',
    ];
    public function getFinalPriceAttribute()
    {
        $price = $this->price_before_discount;
        $localDiscount = $this->discount ?? 0;
        $globalDiscount = optional($this->subCategory)->globalDiscounts()
            ->where('status', 'active')
            ->max('percentage') ?? 0;
        $finalDiscount = max($localDiscount, $globalDiscount);
        return round($price * (1 - $finalDiscount / 100), 2);
    }
    public function subCategory()
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