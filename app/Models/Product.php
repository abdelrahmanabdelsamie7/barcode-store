<?php
namespace App\Models;
use App\traits\{UsesUuid, HasSlug};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\{SubCategory, ProductColor, Color, ProductColorImage, ProductVariant, Size, HomeProduct};

class Product extends Model
{
    use HasFactory, UsesUuid, HasSlug;
    protected $table = 'products';
    protected $fillable = [
        'title',
        'slug',
        'matrial',
        'image_cover',
        'short_description',
        'price_before_discount',
        'status',
        'sub_category_id',
    ];
    public function getSlugSource()
    {
        return 'title';
    }
    protected $casts = [
        'price_before_discount' => 'float',
    ];
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
        return $subCatDiscount;
    }
    public function getFinalPriceAttribute()
    {
        $discount = $this->discount ?? 0;
        $price = (float) $this->price_before_discount;
        return round($price * (1 - $discount / 100), 2);
    }
    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
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
    public function homeProduct()
    {
        return $this->hasOne(HomeProduct::class);
    }
}