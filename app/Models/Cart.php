<?php
namespace App\Models;
use App\Models\{CartItem, User};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'carts';
    protected $fillable = ['user_id', 'visitor_token'];
    protected $appends = ['total_quantity', 'total_price'];
    public function getVisitorTokenAttribute($value)
    {
        return $value ?? null;
    }
    public function setVisitorTokenAttribute($value)
    {
        $this->attributes['visitor_token'] = $value ?? null;
    }
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }
    public function getTotalPriceAttribute()
    {
        return $this->items->collect()->sum(function ($item) {
            return $item->quantity * optional($item->productVariant->product)->final_price;
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
