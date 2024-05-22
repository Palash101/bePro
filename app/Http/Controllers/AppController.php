<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Auth;
use JWTAuth;
class AppController extends Controller
{
    public function __construct()
    {    
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request)
    {
       $request->validate([
        
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'dob' => 'required|date', 
        'gender' => 'required|in:Male,Female',
        'password' => 'required|min:8|confirmed'
        ]);

        try {

        $data = $request->all();           

        $data['password'] = bcrypt($data['password']); 

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
                'user' => $user, // you can ommit this
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

    protected function respondWithToken($token)
    {
        return response()->json($this->respondWithTokenDetails($token));
    }

    protected function respondWithTokenDetails($token)
    {
        
        return [

            'user' => $this->guard()->user(),
            'access_token' => $token,
            'token_type' => 'bearer',
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
}

