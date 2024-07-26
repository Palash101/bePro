<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
use Modules\Theme\App\Models\Theme;
class User extends Authenticatable implements JWTSubject {

    use Notifiable,
        HasRoles,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password','username','gender','profile','banner','password','facebook_id','google_id','dob','status','address','phone','subdomain'
        ,'business_name','members','theme_id','self','category','parent_id','UniqueId'
    ];

    
    protected $guard_name = 'api';


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date'
    ];

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

        public function theme()
        {
            return $this->belongsTo(Theme::class,'theme_id')->select('id','name','image','description','type');
        }

        public function onboarding()
        {
            
            if($this->business_name && $this->members && $this->theme && $this->self && $this->subdomain && $this->category){
                return true;
            }else{
                return false;
            }
        }
   

    

}
