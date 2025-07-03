<?php
namespace App\Models;
use App\traits\{HasSlug,UsesUuid};
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Category extends Model
{
    use HasFactory, UsesUuid , HasSlug;
    protected $table = 'categories';
    protected $fillable = ['name', 'slug', 'is_active'];
    public function getSlugSource()
    {
        return 'name';
    }
    public function sub_categories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}
