<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\ProductColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ProductColorImage extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'product_color_images';
    protected $fillable = ['product_color_id', 'image'];
    public function product()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }
}
