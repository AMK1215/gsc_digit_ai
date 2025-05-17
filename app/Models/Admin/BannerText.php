<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BannerText extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'agent_id',
        'admin_id',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id'); // The admin that owns the banner text
    }

    public function scopeAgent($query)
    {
        return $query->where('agent_id', Auth::user()->id);
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
