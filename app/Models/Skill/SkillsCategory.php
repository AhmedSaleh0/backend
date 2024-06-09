<?php

namespace App\Models\Skill;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillsCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function subCategories()
    {
        return $this->hasMany(SkillsSubCategory::class, 'category_id');
    }

    
    public function skills()
    {
        return $this->hasMany(Skill::class, 'category');
    }
}
