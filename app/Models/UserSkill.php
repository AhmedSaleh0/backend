<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'skill_id'];

    public function users()
{
    return $this->belongsToMany(User::class, 'user_skills');
}
}
