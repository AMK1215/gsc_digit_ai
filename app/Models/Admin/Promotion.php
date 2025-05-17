<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'image', 'agent_id', 'description',
    ];

    protected $appends = ['img_url'];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id'); // The admin that owns the banner
    }

    public function getImgUrlAttribute()
    {
        return 'https://superman788.online/assets/img/promotions/'.$this->image;
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
