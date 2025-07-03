<?php
namespace App\Models;
use App\Models\{Cart, ProductVariant};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use UsesUuid, HasFactory;
    protected $table = 'cart_items';
    protected $fillable = ['cart_id', 'product_variant_id', 'quantity'];
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}