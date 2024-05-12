<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inspire extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'content', 
        'video_url', 
        'user_id', 
        'status', 
        'views', 
        'category', 
        'sub_category'
    ];

    // Relationship with User (assuming each post is associated with a user)
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Optionally, you might have relationships with Category or SubCategory models if those are separate entities
    // public function category() {
    //     return $this->belongsTo(Category::class);
    // }
    // public function subCategory() {
    //     return $this->belongsTo(SubCategory::class);
    // }
}
