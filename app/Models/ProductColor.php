<?php
namespace App\Models;
use App\Models\{Product, Color};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductColor extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'product_colors';
    protected $fillable = ['product_id', 'color_id', 'is_main'];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    public function product_color_images()
    {
        return $this->hasMany(ProductColorImage::class, 'product_color_id');
    }
}