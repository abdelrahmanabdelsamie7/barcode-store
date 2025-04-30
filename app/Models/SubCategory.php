<?php
namespace App\Models;
use App\Models\{Category, Product, Offer};
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCategory extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'sub_categories';
    protected $fillable = ['name', 'slug', 'image', 'category_id', 'is_active'];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function offers()
    {
        return $this->morphMany(Offer::class, 'offerable');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category_id');
    }
}