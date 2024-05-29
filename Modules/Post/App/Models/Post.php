<?php

namespace Modules\Post\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Post\Database\factories\PostFactory;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $fillable = ['user_id','postType','type','title','description','privacy'];

}
