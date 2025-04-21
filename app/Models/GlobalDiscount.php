<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalDiscount extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'global_discounts';
    protected $fillable = [
        'name',
        'percentage',
        'status'
    ];
    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class, 'global_discount_sub_category');
    }
}
