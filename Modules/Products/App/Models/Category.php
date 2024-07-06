<?php

namespace Modules\Products\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Products\Database\factories\CategoryFactory;

class Category extends Model
{

    protected $table = 'pro_categories';
    protected $fillable = ['creator_id','name','status'];
    protected $hidden = [
        'creator_id', 
    ];
}
