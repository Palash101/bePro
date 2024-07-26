<?php

namespace Modules\Products\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Products\Database\factories\ProductFactory;
use Modules\Products\App\Models\Category;
use Modules\Products\App\Models\Brand;
use Modules\Products\App\Models\Variant;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;
    protected $table = 'pro_products';
    protected $fillable = ['creator_id','brand_id','category_id','name','slug','featured_image','description','status','type','UniqueId'];

    protected $hidden = [
        'creator_id','id'
    ];
    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function variants()
    {
        return $this->hasMany(Variant::class,'product_id');
    }
}
