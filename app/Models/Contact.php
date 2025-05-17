<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['link', 'contact_type_id', 'agent_id'];

    public function contact_type()
    {
        return $this->belongsTo(ContactType::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class);
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
