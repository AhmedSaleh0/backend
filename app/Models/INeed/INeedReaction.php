<?php

namespace App\Models\INeed;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class INeedReaction extends Model
{
    use HasFactory;
    protected $table = "i_need_reactions";
    protected $fillable = [
        'ineed_id', 'user_id', 'reaction_type'
    ];

    public function ineed()
    {
        return $this->belongsTo(INeed::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
