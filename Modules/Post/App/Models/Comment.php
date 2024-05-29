<?php

namespace Modules\Post\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Post\Database\factories\CommentFactory;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';
    protected $fillable = ['post_id','client_id','comment'];
}
