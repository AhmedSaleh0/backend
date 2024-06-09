<?php

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\ICan\ICanRequest;
use App\Models\INeed\INeed;
use App\Models\INeed\INeedRequest;
use App\Models\Skill\Skill;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'username', 'country', 'birthdate', 'bio', 'facebook_id', 'google_id'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function image()
    {
        return $this->hasOne(UserImage::class);
    }
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function ican()
    {
        return $this->hasMany(ICanRequest::class);
    }

    public function ineed()
    {
        return $this->hasMany(INeed::class);
    }


    public function icanRequests()
    {
        return $this->hasMany(INeedRequest::class);
    }
    public function ineedRequests()
    {
        return $this->hasMany(INeedRequest::class);
    }
}
