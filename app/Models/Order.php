<?php
namespace App\Models;
use App\Models\{User, OrderItem};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, HasUuids;
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    protected $fillable = [
        'user_id',
        'visitor_token',
        'first_name',
        'last_name',
        'phone',
        'postal_code',
        'address',
        'city',
        'total_price',
        'total_quantity',
        'shipping_cost',
        'status',
        'payment_method',
        'payment_phone',
        'payment_reference',
        'payment_proof',
        'delivered_at',
    ];
    protected $casts = [
        'total_price' => 'float',
        'shipping_cost' => 'float',
        'delivered_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function scopeByVisitor($query, $visitorToken)
    {
        return $query->where('visitor_token', $visitorToken);
    }
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
