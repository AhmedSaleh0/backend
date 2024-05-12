<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ICan extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_title',
        'post_short_description',
        'post_image',
        'post_price',
        'post_price_type',
        'post_status'
    ];

    // If you have relationships, define them here. For example:
    // public function user() {
    //     return $this->belongsTo(User::class);
    // }
}
