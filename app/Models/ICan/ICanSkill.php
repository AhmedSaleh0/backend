<?php

namespace App\Models\ICan;

use App\Models\Skill\Skill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICanSkill extends Model
{
    use HasFactory;

    protected $fillable = ['i_can_id', 'skill_id'];

    public function iCan()
    {
        return $this->belongsTo(ICan::class, 'i_can_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
