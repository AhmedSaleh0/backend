<?php

namespace App\Models\ICan;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICanReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ican_id', 'user_id', 'reaction_type'
    ];

    public function ican()
    {
        return $this->belongsTo(ICan::class, 'ican_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
