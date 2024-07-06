<?php

namespace Modules\Products\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Products\Database\factories\ProductFactory;

class Product extends Model
{
    protected $table = 'pro_categories';
    protected $fillable = ['creator_id','brand_id','category_id','name','slug','featured_image','descriptions','status'];
}
