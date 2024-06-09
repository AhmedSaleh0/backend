<?php

namespace App\Models\INeed;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class INeedRequest extends Model
{
    use HasFactory;

    protected $table = 'i_need_requests';

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
