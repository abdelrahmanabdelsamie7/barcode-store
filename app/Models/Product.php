<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\{SubCategory, Brand, ProductColor};
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
        'price_after_discount',
        'status',
        'sub_category_id',
        'brand_id',
    ];
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
}