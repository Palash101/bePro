<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialDetails extends Model
{
    protected $table = 'social_details';
    protected $fillable = [
        'userDetails',
        'user_id',
        'type','userToken','LongLiveToken','pageAccessToken','pageId','creator_id'
    ];

    protected $hidden = [
        'creator_id','id'
    ];
}
