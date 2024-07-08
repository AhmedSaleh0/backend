<?php

namespace App\Models\Common;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rated_id',
        'type',
        'rating',
        'review',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rated()
    {
        return $this->belongsTo(User::class, 'rated_id');
    }
}
