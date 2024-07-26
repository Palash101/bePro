<?php

namespace Modules\Products\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Products\Database\factories\VariantFactory;

class Variant extends Model
{
    protected $table = 'pro_product_variants';
    protected $fillable = ['creator_id','UniqueId'	,'product_id','variant_name','images','banner_images','text','type','price','discount','discount_price','descriptions','current_stock','upcoming_stock','status'];
   
    protected $hidden = [
        'creator_id','id'
    ];
}
