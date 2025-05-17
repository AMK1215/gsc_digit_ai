<?php

namespace App\Models\Admin;

use App\Models\BankAgent;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = ['account_name', 'account_number', 'payment_type_id', 'status'];

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function bankAgents()
    {
        return $this->hasMany(BankAgent::class);
    }

    public function scopeAgent($query)
    {
        return $query->whereHas('bankAgents', function ($query) {
            $query->where('agent_id', Auth::id());
        });
    }

    public function scopeAgentPlayer($query)
    {
        return $query->whereHas('bankAgents', function ($query) {
            $query->where('agent_id', Auth::user()->agent_id);
        });
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();

        return $query->whereHas('bankAgents', function ($query) use ($agents) {
            $query->whereIn('agent_id', $agents);
        });
    }
}
