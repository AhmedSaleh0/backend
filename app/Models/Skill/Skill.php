<?php

namespace App\Models\Skill;

use App\Models\ICan\ICan;
use App\Models\INeed\INeed;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    
    protected $table = "skills";
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
    public function atICan()
    {
        return $this->belongsToMany(ICan::class, 'i_can_skills', 'skill_id', 'i_can_id');
    }

    public function atINeed()
    {
        return $this->belongsToMany(INeed::class, 'i_need_skills', 'skill_id', 'i_need_id');
    }
}
