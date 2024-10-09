<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body', 'user_id'];
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
