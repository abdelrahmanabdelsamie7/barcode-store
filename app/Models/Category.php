<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Category extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'categories';
    protected $fillable = ['name', 'slug', 'image', 'is_active'];
    public function sub_categories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}