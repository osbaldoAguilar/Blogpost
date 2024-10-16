<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Post;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];
    /**
     * The attributes for the avatar column
     */
    // protected function avatar(): Attribute
    // {
    //     return Attribute::make(get: function ($value) {
    //         return $value ? '/storage/avatars' : 'fallback-avatar.jpg';
    //     });
    // }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Return the avatar path if it exists, otherwise return the fallback image
                return $value ? "/storage/avatars/{$value}" : '/fallback-avatar.jpg';
            }
        );
    }


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


    public function feedUserPosts()
    {
        return $this->hasManyThrough(
            Post::class, // The final 
            Follow::class, // the intermediate "table that has relationship that you have to look up"
            'user_id', // foreign key intermediate
            'user_id', //foreign key final 
            'id', // local key final 
            'follows_user' // local key intermediate
        );
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'follows_user');
    }

    public function following()
    {
        return $this->hasMany(Follow::class, 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}
