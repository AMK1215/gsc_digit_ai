<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiGameResult extends Model
{
    use HasFactory;
    // Define the table if it's not the plural of the model name
    // protected $table = 'game_results';

    // Specify which attributes are mass assignable
    protected $fillable = [
        'period',
        'duration',
        'winning_digit',
        'winning_color',
        'winning_size',
        // Add other fillable fields here
    ];

    // Cast attributes to native types
    protected $casts = [
        // 'winning_digit' => 'integer', // Already integer type in migration
        // 'duration' => 'integer', // Already integer type in migration
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
    ];

    // Define any relationships if needed (e.g., hasMany bets)
    // public function bets()
    // {
    //     return $this->hasMany(Bet::class, 'period', 'period'); // Assuming bets table links by period string
    // }
}