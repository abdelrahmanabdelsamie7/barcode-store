<?php
namespace App\Models;
use App\Models\{User, OrderItem, UserDiscountCode};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, HasUuids;
    const STATUS_PENDING = 'Pending';
    const STATUS_CONFIRMED = 'Confirmed';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_SHIPPED = 'Shipped';
    const STATUS_CANCELLED = 'Cancelled';
    protected $fillable = [
        'user_id',
        'visitor_token',
        'first_name',
        'last_name',
        'cart_id',
        'phone',
        'address',
        'city',
        "status",
        'total_price',
        'total_quantity',
        'shipping_cost',
        'payment_method',
        'payment_phone',
        'payment_reference',
        'payment_proof',
        'user_discount_code_id',
        'discount_amount',
        'delivered_at',
        'shipped_at'
    ];
    protected $casts = [
        'total_price' => 'float',
        'shipping_cost' => 'float',
        'delivered_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function userDiscountCode()
    {
        return $this->belongsTo(UserDiscountCode::class,'user_discount_code_id');
    }
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }
    public function scopeByVisitor($query, $visitorToken)
    {
        return $query->where('visitor_token', $visitorToken);
    }
    public function getCommissionAmountAttribute()
    {
        return $this->status === self::STATUS_CONFIRMED
            ? round(($this->total_price_after_shipping) * 0.1, 2)
            : 0;
    }
}