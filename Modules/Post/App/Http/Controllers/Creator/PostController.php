<?php

namespace Modules\Post\App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Post\App\Models\Post;
use Modules\Post\App\Models\Comment;
use Modules\Post\App\Models\Like;
use Modules\Post\App\Models\Attachment;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

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
           // 'type' => 'required|in:Paid,Free',
           // 'postType' => 'required|in:Text,Video,Audio,Image,Link,Poll,Livestream',
           // 'status' => 'required|in:Active,Blocked,Draft',
        ]);

        $data = $request->except('_token');

        try {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        $data['status'] = 'Draft';
        
        
        
        

        Post::create($data);
        return response(['status' => 'success','msg'=>"Post created successfully"],200);
        

        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }

    public function update(Request $request, $id)
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

            $package = Post::findOrFail($id);  
            foreach($request->attachments as $attachments){
                $url = $attachments;
                $image = Image::make($url);
                $image->blur(100);
                $path = 'images/' . uniqid() . '.jpg';
                Storage::disk('public')->put($path, (string) $image->encode());
                $urlAttachment = Storage::disk('public')->url($path);
                
                    $attachmentsSave = [
                        'post_id' => $package->id,
                        'attachment' => $attachments,
                        'blur_attachment' => $urlAttachment,
                        ];                
                        Attachment::create($attachmentsSave);
                }        
            $package->update($data);            
            return response(['status' => 'success','msg'=>'Post updated successfully'],200);
           
            
        } catch (\Exception $e) {
            return response(['status' => 'success','msg'=>$e->getMessage()],401);
        }

    }

    public function addComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $data = $request->except('_token');
        try {
        $user = auth()->user();
        $data['client_id'] = $user->id;
        $data['post_id'] = $id;
        $checkComment = Comment::whereClientId($user->id)->wherePostId($id)->first();
        if(!empty($checkComment)){
            return response(['status' => 'error','msg'=>"Sorry! already comment in this post"],401);
        }
        Comment::create($data);
        return response(['status' => 'success','msg'=>"Comment created successfully"],200);
        } catch (\Exception $e) {
            return response(['status' => 'error','msg'=>$e->getMessage()],401);
        }
    }

    public function addLike(Request $request, $id)
    {
        $data = $request->except('_token');
        try {
        $user = auth()->user();
        $data['client_id'] = $user->id;
        $data['post_id'] = $id;
        $data['like'] = 1;
        $checkLike = Like::whereClientId($user->id)->wherePostId($id)->first();
        if(!empty($checkLike)){
            return response(['status' => 'error','msg'=>"Sorry! already liked in this post"],401);
        }
        Like::create($data);
        return response(['status' => 'success','msg'=>"Post liked successfully"],200);
        } catch (\Exception $e) {
            return response(['status' => 'error','msg'=>$e->getMessage()],401);
        }
    }

    public function UnLike(Request $request, $id)
    {
        $data = $request->except('_token');
        try {
        $user = auth()->user();
        $checkLike = Like::whereClientId($user->id)->wherePostId($id)->first();

        if (!empty($checkLike)) {
            $checkLike->delete();
            return response(['status' => 'success','msg'=>"Post unliked successfully"],200);
           
        }
        return response(['status' => 'success','msg'=>"Post liked successfully"],200);
        } catch (\Exception $e) {
            return response(['status' => 'error','msg'=>$e->getMessage()],401);
        }
    }
}
