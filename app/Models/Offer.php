<?php
namespace App\Models;
use App\traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Offer extends Model
{
    use HasFactory,UsesUuid;
    protected $table = 'offers';
    protected $fillable = ['discount', 'offerable_type', 'offerable_id', 'start_at', 'end_at'];
    public function offerable()
    {
        return $this->morphTo()
            ->where(function ($query) {
                $query->where('offerable_type', 'product')
                    ->orWhere('offerable_type', 'subcategory');
            });
    }
}