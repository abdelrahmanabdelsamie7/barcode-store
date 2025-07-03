<?php
namespace App\Models;
use App\Models\{Cart, Order,Wishlist};
use App\traits\UsesUuid;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, UsesUuid;
    protected $fillable = [
        'first_name',
        'last_name',
        'whatsapp_phone',
        'password',
        'phone_verification_code',
        'phone_verified_at',
    ];
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    protected $hidden = [
        'password',
        'phone_verification_code',
    ];
    protected static function boot()
    {
        parent::boot();
        static::created(function ($user) {
            $user->cart()->create();
        });
    }
    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }
    public function discountCodes()
    {
        return $this->hasMany(UserDiscountCode::class);
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function isPhoneVerified()
    {
        return !is_null($this->phone_verified_at);
    }
}
