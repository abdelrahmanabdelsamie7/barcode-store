<?php
namespace App\Models;
use App\Models\Product;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'brands';
    protected $fillable = ['name', 'slug', 'image', 'is_active'];
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
