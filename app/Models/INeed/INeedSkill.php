<?php

namespace App\Models\INeed;

use App\Models\Skill\Skill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class INeedSkill extends Model
{
    use HasFactory;

    protected $fillable = ['i_need_id', 'skill_id'];

    public function iNeed()
    {
        return $this->belongsTo(INeed::class, 'i_need_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }}
