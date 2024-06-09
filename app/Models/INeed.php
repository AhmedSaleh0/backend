<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class INeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'short_description',
        'image',
        'price',
        'price_type',
        'status',
        'location',
        'experience'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function requests()
    {
        return $this->hasMany(INeedRequest::class, 'ineed_id');
    }
}
