<?php

namespace App\Models\ICan;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ICan extends Model
{
    use HasFactory;
    protected $table = "i_can";

    protected $fillable = [
        'title',
        'short_description',
        'image',
        'price',
        'price_type',
        'status',
        'location',
        'experience'
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

    /**
     * Get the user that owns the ICan post.
     */    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the requests for the ICan post.
     */
    public function requests()
    {
        return $this->hasMany(ICanRequest::class, 'ican_id');
    }
}
