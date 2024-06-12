<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Auth;
use JWTAuth;
use App\Http\Resources\UserResource;
use App\Http\Traits\FileUpload;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use App\Http\Traits\Domain;
class AppController extends Controller
{
    use FileUpload,Domain;

    public function __construct()
    {    
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request)
    {
       $request->validate([
        'email' => 'required|email|unique:users',
        //'username' => 'required|username|unique:users',
        'password' => 'required|min:8'
        ]);


        try {

        $data = $request->all();           

        $data['password'] = bcrypt($data['password']); 
        $data['status'] = 1; 

        $user = User::create($data);             
        $role = Role::find(2);
        $user->assignRole($role);

        if($user)
        {    
            $credentials = ['email' => $user->email , 'password' => $request->password];
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }

            return response()->json([
                'message' => 'User register successfully',
                'token' => $token,
                'user' => new UserResource($user), // you can ommit this
            ]);       
        }
        
        } catch (\Exception $e) {         
        return response(['status' => 'error','message' => 'Something went wrong!','dev_msg' => $e->getMessage()],403);        
        }
    }
    

    public function login(Request $request)
    {
       
        $credentials = $request->only('email', 'password');        
        $user = User::where('email',$credentials['email'])->first();               
        $roles = Role::with(['permissions'])->whereIn('name',['User'])->get(); 
        
        if ($user && $user->hasRole($roles) &&  $user->status  && $token = $this->guard()->attempt($credentials)) {
       
            return $this->respondWithToken($token);    

        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function checkSubdomian(Request $request)    {   
       

        
        try {
            $user = $this->guard()->user();
            $subdomain = "$request->domain.beprocreators.com";
            $checkSubdomain = User::where('subdomain',$subdomain)->first();
            
            if(!empty($checkSubdomain)){
                return response(['status' => 'error','msg' => 'The domain name you entered already exists; please choose a different one.'],401); 
            }else{
                $user->subdomain = $subdomain;
                $user->save();
                $body = [
                    "name" => $subdomain                   
                ];
                $encodedData = json_encode($body);
                $this->subDomainCheck($encodedData); 
                
                return response(['status' => 'success','msg' => 'Congratulations! Your domain has been successfully added.'],200); 
            }
        
        } catch (\Exception $e) {
            
              return response(['status' => 'error','msg' => 'Something went wrong','dev_msg' => $e->getMessage()],404);
        }
    }

    protected function respondWithToken($token)
    {
        
    $minutes = 60;
    $domain = "beprocrators.com";
    $response = new Response('Set Cookie');
   
        return response()->json($this->respondWithTokenDetails($token))->withCookie(cookie('token', $token, $minutes, '/', null, false, false));
    }

    protected function respondWithTokenDetails($token)
    {
        
        return [
            'user' => new UserResource($this->guard()->user()),
            'token_type' => 'bearer',
            'token' => $token,
            'expires_in' => $this->guard()->factory()->getTTL() * 60

        ];
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    public function profile(Request $request)
    {   
        $user = $this->guard()->user();
        try {
            $user = User::findOrFail($user->id);
            return response(['status' => 'success','user' => new UserResource($user)],200);
        
        } catch (\Exception $e) {
            
              return response(['status' => 'error','msg' => 'Something went wrong','dev_msg' => $e->getMessage()],404);

        }

    }

    public function profileUpdate(Request $request)
    {
        $user = $this->guard()->user();
        try {
        $data = $request->all();
        $user = User::findOrFail($user->id);
         if($user)
           {    
            if ($request->hasFile('profile')) {
                $pathToUpload = 'uploads/user/';
                $file = $request->file('profile');
                $data['profile'] = $this->uploadFile($pathToUpload, $file);
            }
            
            if ($request->hasFile('banner')) {
                $pathToUpload = 'uploads/user/';
                $file = $request->file('banner');
                $data['banner'] = $this->uploadFile($pathToUpload, $file);
            }
            if ($request->gender) {
                $data['gender'] = $request->gender;
            }
            if ($request->name) {
                $data['name'] = $request->name;
            }
            if ($request->phone) {
                $data['phone'] = $request->phone;
            }
            if ($request->dob) {
                $data['dob'] = $request->dob;
            }
            if ($request->address) {
                $data['address'] = $request->address;
            }
            $user->update($data);
            return response(['status' => 'success' ,'user' => new UserResource($user),'message' => 'Profile updated successfully'], 200);
        }

        } catch (\Exception $e) {
         
        return response(['status' => 'error','message' => 'Something went wrong!','dev_msg' => $e->getMessage()],403);        

        }
    }
}

