<?php
namespace App\Models;
use App\traits\UsesUuid;
use App\Models\{DiscountCampaign, User};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDiscountCode extends Model
{
    use HasFactory,UsesUuid;
    protected $fillable = [
        'id',
        'code',
        'user_id',
        'campaign_id',
        'is_used',
        'used_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function campaign()
    {
        return $this->belongsTo(DiscountCampaign::class, 'campaign_id');
    }
}