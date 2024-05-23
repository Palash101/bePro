<?php

namespace Modules\Packages\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Packages\Database\factories\PackagesFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packages extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'packages';
    protected $fillable = ['name','user_id','description','amount','date','discount','type','status','image'];

}
