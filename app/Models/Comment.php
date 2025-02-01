<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Reply;

class Comment extends Model
{
    use HasFactory;
    protected $fillable =[
        'user_id',
        'commentable_id',
        'commentable_type',
        'content',
        'like',
        'active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }
    public function replies()
    {
        return $this->belongsToMany(Reply::class, 'comment_reply', 'comment_id', 'reply_id');
    }

}
