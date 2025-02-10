<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedPost extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'owner_id', 'shared_to'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function sharedTo()
    {
        return $this->belongsTo(User::class, 'shared_to');
    }
}
