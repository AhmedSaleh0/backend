<?php

namespace App\Models\ICan;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ICan extends Model
{
    use HasFactory;
    protected $table = "i_can";

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

    // If you have relationships, define them here. For example:
    public function user() {
        return $this->belongsTo(User::class);
    }
}
