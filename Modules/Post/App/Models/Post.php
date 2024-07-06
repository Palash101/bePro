<?php

namespace Modules\Post\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Post\Database\factories\PostFactory;
use Modules\Post\App\Models\Attachment;
class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    protected $fillable = ['user_id','postType','type','title','description','privacy','preview_image'];


    public function attachments()
        {
            return $this->hasMany(Attachment::class,'post_id')->select('id','post_id','attachment');
        }

}
