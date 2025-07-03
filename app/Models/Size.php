<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Size extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'sizes';
    protected $fillable = ['name', 'order'];
    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'size_id');
    }
}