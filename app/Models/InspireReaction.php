<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspireReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspire_id', 'user_id', 'reaction_type'
    ];

    public function inspire()
    {
        return $this->belongsTo(Inspire::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
