<?php

namespace Modules\Products\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Products\Database\factories\BrandFactory;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'pro_brands';
    protected $fillable = ['creator_id','name','image','descriptions','status'];
    protected $hidden = [
        'creator_id', 
    ];
    
    
}
