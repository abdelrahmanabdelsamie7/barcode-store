<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\{User,Product};

class Wishlist extends Model
{
    use HasFactory,UsesUuid;
    protected $table = 'wishlists';
    protected $fillable = [
        'user_id',
        'visitor_token',
        'product_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}