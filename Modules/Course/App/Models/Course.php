<?php

namespace Modules\Course\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Course\Database\factories\CourseFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Course\App\Models\Category;
class Course extends Model
{
    use SoftDeletes;
    protected $table = 'co_courses';
    protected $fillable = ['creator_id','UniqueId','category_id','title','slug','featured_image','descriptions','payment_type','price','discount','discount_price','status'];
    protected $hidden = [
        'creator_id', 
    ];

    
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }


}
