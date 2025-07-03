<?php
namespace App\Models;
use App\Models\{Category, Product, Offer, DiscountCampaign} ;
use App\traits\{HasSlug, UsesUuid};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCategory extends Model
{
    use HasFactory, UsesUuid, HasSlug;
    protected $table = 'sub_categories';
    protected $fillable = ['name', 'slug', 'image', 'category_id', 'is_active'];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function getSlugSource()
    {
        return 'name';
    }
    public function offers()
    {
        return $this->hasMany(Offer::class, 'sub_category_id');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category_id');
    }
    public function discountCampaigns(){
        return $this->hasMany(DiscountCampaign::class, 'sub_category_id');
    }
}
