<?php

namespace Modules\Post\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Post\Database\factories\LikeFactory;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';
    protected $fillable = ['post_id','client_id','like'];

}
