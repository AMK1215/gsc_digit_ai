<?php

namespace App\Models;

use App\Enums\UserType;
use App\Events\UserCreatedEvent;
use App\Models\Admin\Bank;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Models\Admin\Permission;
use App\Models\Admin\Promotion;
use App\Models\Admin\Role;
use App\Models\Admin\UserLog;
use App\Models\SeamlessTransaction;
use App\Models\Webhook\Bet;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements Wallet
{
    use HasApiTokens, HasFactory, HasWalletFloat, Notifiable;

    private const PLAYER_ROLE = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'name',
        'profile',
        'email',
        'password',
        'profile',
        'phone',
        'balance',
        'max_score',
        'agent_id',
        'status',
        'session_id',
        'type',
        'referral_code',
        'commission',
    ];

    protected $dispatchesEvents = [
        'created' => UserCreatedEvent::class,
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'type' => UserType::class,
    ];

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function getIsMasterAttribute()
    {
        return $this->roles()->where('id', 2)->exists();
    }

    public function getIsAgentAttribute()
    {
        return $this->roles()->where('id', 3)->exists();
    }

    public function getIsUserAttribute()
    {
        return $this->roles()->where('id', 4)->exists();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasRole($role)
    {
        return $this->roles->contains('title', $role);
    }

    public function hasPermission($permission)
    {
        return $this->roles->flatMap->permissions->pluck('title')->contains($permission);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Other users that this user (a master) has created (agents)
    public function createdAgents()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    // The master that created this user (an agent)
    public function createdByMaster()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public static function adminUser()
    {
        return self::where('type', UserType::Admin)->first();
    }

    public function seamlessTransactions()
    {
        return $this->hasMany(SeamlessTransaction::class, 'user_id');
    }

    public function wagers()
    {
        return $this->hasMany(Wager::class);
    }

    public function scopeRoleLimited($query)
    {
        if (! Auth::user()->hasRole('Admin')) {
            return $query->where('agent_id', Auth::id());
        }

        return $query;
    }

    public function userPayments()
    {
        return $this->hasMany(UserPayment::class);
    }

    public function scopePlayer($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('role_id', self::PLAYER_ROLE);
        });
    }

    public static function getPlayersByAgentId(int $agentId)
    {
        return self::where('agent_id', $agentId)
            ->whereHas('roles', function ($query) {
                $query->where('title', '!=', 'Agent');
            })
            ->get();
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class, 'agent_id');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'payable');
    }

    public function bets()
    {
        return $this->hasMany(Bet::class, 'user_id');
    }

    // Fetch agents created by an admin
    // public function createdAgents()
    // {
    //     return $this->hasMany(User::class, 'agent_id');
    // }

    // Fetch players managed by an agent
    public function players()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // A user can have a parent (e.g., Agent belongs to an Admin)
    public function parent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // A user can have children (e.g., Admin has many Agents, or Agent has many Players)
    public function children()
    {
        return $this->hasMany(User::class, 'agent_id');
    }

    // Get all players under an agent
    public function Agentplayers()
    {
        return $this->children()->whereHas('roles', function ($query) {
            $query->where('name', 'Player'); // Assuming you have roles for users
        });
    }

    public function banners()
    {
        return $this->hasMany(Banner::class, 'agent_id'); // Banners owned by this admin
    }

    public function bannertexts()
    {
        return $this->hasMany(BannerText::class, 'agent_id'); // Banners owned by this admin
    }

    public function bannerads()
    {
        return $this->hasMany(BannerAds::class, 'agent_id'); // Banners owned by this admin
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'agent_id'); // Banners owned by this admin
    }

    public function agents()
    {
        return $this->hasMany(User::class, 'agent_id'); // Banners owned by this admin
    }

    public function scopeAgent($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('title', 'Agent'); // Assuming you have roles for users
        })->where('agent_id', Auth::user()->id);
    }

    public function userLog()
    {
        return $this->hasMany(UserLog::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now('Asia/Yangon');
            $model->updated_at = Carbon::now('Asia/Yangon');
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now('Asia/Yangon');
        });
    }
}
