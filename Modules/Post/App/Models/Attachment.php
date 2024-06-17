<?php

namespace Modules\Post\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Post\Database\factories\AttachmentFactory;

class Attachment extends Model
{
    use HasFactory;
    protected $table = 'post_attachments';
    protected $fillable = ['post_id','attachment','blur_attachment'];

}
