<?php

namespace Modules\Post\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Post\App\Models\Post;

class PostController extends Controller
{
    public function getPost(Request $request)
    {
        $user = auth()->user();
        $packages = Post::whereUserId($user->id)->orderBy('created_at','desc')->get();
        return response(['status' => 'success','packages'=>$packages],200);
    }

    public function addPost(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:Paid,Free',
            'postType' => 'required|in:Text,Video,Audio,Image,Link,Poll,Livestream',
            'status' => 'required|in:Active,Blocked,Draft',
        ]);

        $data = $request->except('_token');

        try {
        $user = auth()->user();
        $data['user_id'] = $user->id;

        // if ($request->hasFile('image')) {
        //     $pathToUpload = 'uploads/package/';
        //     $file = $request->file('image');
        //     $data['image'] = $this->uploadFile($pathToUpload, $file);
        // }
        Post::create($data);
        return response(['status' => 'success','msg'=>"Post created successfully"],200);
        

        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }
}
