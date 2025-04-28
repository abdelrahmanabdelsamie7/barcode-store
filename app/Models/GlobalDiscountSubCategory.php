<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\{GlobalDiscount, SubCategory};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalDiscountSubCategory extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'global_discount_sub_category';
    protected $fillable = [
        'global_discount_id',
        'sub_category_id',
    ];
    public function globalDiscount()
    {
        return $this->belongsTo(GlobalDiscount::class, 'global_discount_id');
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}