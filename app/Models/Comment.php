<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BlogPost;
use App\Models\User;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['text', 'author_id', 'blog_post_id', 'author_id'];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'updated_at'];
 
    public function post()
    {
        return $this->belongsTo(BlogPost::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
