<?php

namespace App\Models\User;

use App\Models\Common\Rating;
use App\Models\Communications\Conversation;
use App\Models\Communications\Message;
use App\Models\ICan\ICanRequest;
use App\Models\INeed\INeed;
use App\Models\INeed\INeedRequest;
use App\Models\Skill\Skill;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Notifications\VerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = "users";

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'country_code', 'username', 'country', 'birthdate', 'bio', 'facebook_id', 'google_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
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

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'user_one_id')
            ->orWhere('user_two_id', $this->id);
    }


    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'rated_id');
    }
}
