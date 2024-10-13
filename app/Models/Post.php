<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;
    use HasFactory;

    protected $fillable = ['title', 'body', 'user_id'];

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'body' => $this->body
        ];
    }

    // Added to for the association to the user table
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Associtaion by the user_id
    public function user_instance()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
