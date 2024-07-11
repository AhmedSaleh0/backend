<?php

namespace App\Models\INeed;

use App\Models\Common\Rating;
use App\Models\Skill\Skill;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class INeed extends Model
{
    use HasFactory;

    protected $table = "i_need";
    protected $fillable = [
        'title',
        'short_description',
        'image',
        'price',
        'price_type',
        'status',
        'location',
        'experience',
        'user_id'
    ];
    /**
     * Set the experience attribute.
     *
     * @param string $value
     * @return void
     */
    public function setExperienceAttribute($value)
    {
        $allowedValues = ['Entry', 'Intermediate', 'Expert'];
        if (!in_array($value, $allowedValues)) {
            throw new \InvalidArgumentException("Invalid experience value");
        }
        $this->attributes['experience'] = $value;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requests()
    {
        return $this->hasMany(INeedRequest::class, 'ineed_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'i_need_skills', 'i_need_id', 'skill_id');
    }

    public function reactions()
    {
        return $this->hasOne(INeedReaction::class, 'ineed_id');
    }

    public function isLikedByUser()
    {
        return $this->reactions()->where('user_id', Auth::id())->exists();
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    /**
     * Get the connection status attribute.
     * 
     * @return string
     */
    public function getConnectionStatusAttribute()
    {
        $request = $this->requests()->where('user_id', Auth::id())->first();
        return $request ? $request->status : 'notConnected';
    }
}
