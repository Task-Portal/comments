<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\File;

class Comment extends Model
{
    protected $fillable = ['content', 'parent_id', 'commented_comment_id', 'picture_file_path', 'text_file_path', 'level','url'];

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function commentedComment()
    {
        return $this->belongsTo(Comment::class, 'commented_comment_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function files(){
        return $this->hasMany(File::class);
    }

}
