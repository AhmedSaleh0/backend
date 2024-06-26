<?php

namespace App\Models\Skill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillsSubCategory extends Model
{
    use HasFactory;

    protected $table = "skills_sub_categories";
    protected $fillable = ['category_id', 'name'];

    public function category()
    {
        return $this->belongsTo(SkillsCategory::class, 'category_id');
    }

    
    public function skills()
    {
        return $this->hasMany(Skill::class, 'sub_category');
    }
}
