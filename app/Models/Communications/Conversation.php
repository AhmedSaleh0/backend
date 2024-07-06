<?php

namespace App\Models\Communications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_one_id', 'user_two_id'];

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getOtherUserNameAttribute()
    {
        $otherUserId = $this->user_one_id == auth()->id() ? $this->user_two_id : $this->user_one_id;
        $otherUser = User::find($otherUserId);
        return $otherUser ? "{$otherUser->first_name} {$otherUser->last_name}" : null;
    }

    public function getOtherUserImageAttribute()
    {
        $otherUserId = $this->user_one_id == auth()->id() ? $this->user_two_id : $this->user_one_id;
        $otherUser = User::find($otherUserId);
        return $otherUser->image ? $otherUser->image->image_path : null;
    }

    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }
}
