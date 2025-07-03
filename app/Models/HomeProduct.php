<?php
namespace App\Models;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomeProduct extends Model
{
    use HasFactory,HasUuids;
    protected $table = 'home_products';
    protected $fillable = ['product_id', 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
