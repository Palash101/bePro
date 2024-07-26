<?php

namespace Modules\Packages\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Packages\Database\factories\UserPackagesFactory;

class UserPackages extends Model
{
    use HasFactory;

    protected $table = 'user_packages';
    protected $fillable = ['package_id','user_id','client_id','amount','name','discount','purchase_date','expire_date','UniqueId'];
}
