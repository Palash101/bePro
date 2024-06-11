<?php

namespace Modules\Theme\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Theme\Database\factories\ThemeFactory;

class Theme extends Model
{
    use HasFactory;

    protected $table = 'theme';
    protected $fillable = ['name','description','image','type','status'];
    
    
}
