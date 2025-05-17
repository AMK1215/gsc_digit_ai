<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerAds extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile_image',
        'desktop_image',
        'description',
        'agent_id',
        'admin_id',
    ];

    protected $appends = ['mobile_image_url', 'desktop_image_url'];

    protected $table = 'banner_ads';

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id'); // The admin that owns the banner
    }

    public function getMobileImageUrlAttribute()
    {
        return 'https://superman788.online/assets/img/banners_ads/'.$this->mobile_image;
    }

    public function getDesktopImageUrlAttribute()
    {
        return 'https://superman788.online/assets/img/banners_ads/'.$this->desktop_image;
    }

    public function scopeAgent($query)
    {
        return $query->where('agent_id', auth()->user()->id);
    }

    public function scopeAgentPlayer($query)
    {
        return $query->where('agent_id', auth()->user()->agent_id);
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();

        return $query->whereIn('agent_id', $agents);
    }
}
