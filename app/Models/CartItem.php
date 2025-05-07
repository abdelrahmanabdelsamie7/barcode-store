<?php
namespace App\Models;
use App\Models\{Cart, ProductVariant};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'cart_items';
    protected $fillable = ['cart_id', 'product_variant_id', 'quantity', 'price'];
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function getTotalPriceAttribute()
    {
        return $this->price * $this->quantity;
    }
    public function updateQuantity(int $quantity)
    {
        $this->quantity = $quantity;
        $this->save();
        $this->cart->updateTotalPrice();
    }
}
