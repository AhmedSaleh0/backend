<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'category', 'sub_category'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills');
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
