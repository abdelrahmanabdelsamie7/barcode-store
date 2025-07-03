<?php
namespace App\Models;
use App\Models\{Order, ProductVariant};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'price',
    ];
    protected $casts = [
        'price' => 'float',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    
}
