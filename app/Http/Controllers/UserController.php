<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Auth;
use JWTAuth;
class UserController extends Controller
{
    public function __construct()
    {    
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request)
    {
       $request->validate([
        'email' => 'required|email',
        'domain' => 'required|string',
        'password' => 'required|min:8'
        ]);


        try {

        $data = $request->all();           

        $data['password'] = bcrypt($data['password']); 
        $data['status'] = 1; 

        $checkSubdomain = User::where('subdomain',$request->domain)->first();
        if(!$checkSubdomain){
            return response(['status' => 'error','message' => 'The domain you are trying to reach does not exist; please check the address and try again.'],403);    
        }
       
        $data['parent_id'] = $checkSubdomain->id;
        $user = User::create($data);             
        $role = Role::find(1);
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

    public function guard()
    {
        return Auth::guard('api');
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

}

