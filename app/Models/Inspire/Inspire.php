<?php

namespace App\Models\Inspire;

use App\Models\Skill\SkillsCategory;
use App\Models\Skill\SkillsSubCategory;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Inspire extends Model
{
    use HasFactory;

    protected $table = "inspire";
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
    public function user()
    {
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

    public function reactions()
    {
        return $this->hasOne(InspireReaction::class);
    }
    public function comments()
    {
        return $this->hasMany(InspireComment::class);
    }

    public function isLikedByUser()
    {
        return $this->reactions()->where('user_id', Auth::id())->exists();
    }
}
