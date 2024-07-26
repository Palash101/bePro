<?php

namespace Modules\Course\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Course\Database\factories\CategoryFactory;

class Category extends Model
{
    protected $table = 'co_categories';
    protected $fillable = ['creator_id','name','status','UniqueId'];
    protected $hidden = [
        'creator_id', 
    ];
}
