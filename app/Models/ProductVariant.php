<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\{ProductColor, Product, Size};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'product_variants';
    protected $fillable = ['product_color_id', 'size_id', 'quantity'];
    public function product_color()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

}
