<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory, UsesUuid;
    protected $table = 'offers';
    protected $fillable = ['discount', 'sub_category_id', 'start_at', 'end_at'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function SubCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
