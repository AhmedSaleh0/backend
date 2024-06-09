<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class INeedRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ineed_id',
        'user_id',
        'status',
    ];

    public function ineed()
    {
        return $this->belongsTo(INeed::class, 'ineed_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
