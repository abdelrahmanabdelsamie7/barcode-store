<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\ProductColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Color extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'colors';
    protected $fillable = ['name', 'hex_code'];
    public function product_colors()
    {
        return $this->hasMany(ProductColor::class, 'color_id');
    }
}