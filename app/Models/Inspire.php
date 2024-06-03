<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inspire extends Model
{
    use HasFactory;

    protected $table ="inspire";
    protected $fillable = [
        'type', 
        'title', 
        'content', 
        'media_url', 
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

    public function category()
    {
        return $this->belongsTo(SkillsCategory::class, 'category');
    }

    public function subCategory()
    {
        return $this->belongsTo(SkillsSubCategory::class, 'sub_category');
    }
}
