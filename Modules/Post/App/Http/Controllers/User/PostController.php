<?php

namespace Modules\Post\App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Post\App\Models\Post;
class PostController extends Controller
{
    public function getPost(Request $request)
    {
        
        $post = Post::with('attachments')->orderBy('created_at','desc')->get();
        return response(['status' => 'success','post'=>$post],200);
    }  
}
