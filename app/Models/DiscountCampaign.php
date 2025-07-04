<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\{UserDiscountCode, SubCategory};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCampaign extends Model
{
    use HasFactory,UsesUuid;
    protected $fillable = [
        'id',
        'name',
        'type',
        'discount_type',
        'discount_value',
        'min_order_value',
        'start_at',
        'end_at',
    ];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
    public function userDiscountCodes()
    {
        return $this->hasMany(UserDiscountCode::class, 'campaign_id');
    }
}