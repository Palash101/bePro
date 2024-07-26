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
use App\Models\User;
use App\Http\Traits\UniqueId;

class PostController extends Controller
{
    use UniqueId;
    public function getPost(Request $request)
    {
        $user = auth()->user();
        $post = Post::with('attachments')->whereUserId($user->id)->orderBy('created_at','desc')->get();
        return response(['status' => 'success','post'=>$post],200);
    }

    public function getPostbyDomain(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|exists:users,subdomain',
        ]);
        $checkSubdomain = User::where('subdomain',$request->domain)->first();
        $post = Post::with('attachments')->whereUserId($checkSubdomain->id)->orderBy('created_at','desc')->get();
        return response(['status' => 'success','post'=>$post],200);
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
        $unique =  $this->generateUniqueId();
        $data['user_id'] = $user->id;
        $data['status'] = 'Draft';
        $data['UniqueId'] = $unique;
        $post =  Post::create($data);
        return response(['status' => 'success','id'=>$unique,'msg'=>"Post created successfully"],200);
        
        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            
           // 'title' => 'required|string',
            //'description' => 'nullable|string',
            'type' => 'required|in:Paid,Free',
            'postType' => 'required|in:Text,Video,Audio,Image,Link,Poll,Livestream',
           // 'status' => 'required|in:Active,Blocked,Draft',
        
        ]);

        $data = $request->except('_token');

        try {

            $post = Post::where('UniqueId', $id)->first();  
           //dd() dd($request);
           if(!empty($request->postImages)){
           $deleted = Attachment::wherePostId($post->id)->delete();
           }
            foreach($request->postImages as $attachments){
                
                $unique =  $this->generateUniqueId();
                    $attachmentsSave = [
                        'post_id' => $post->id,
                        'UniqueId' => $unique,
                        'attachment' => $attachments,
                        
                       // 'blur_attachment' => $urlAttachment,
                        ];                
                        Attachment::create($attachmentsSave);
                }    
               $data['preview_image'] =  $request['previewImage'];    
            $post->update($data);            
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

    public function show($id)
    {
        try {
            $user = auth()->user();
            $post = Post::whereUserId($user->id)->where('UniqueId', $id)->with('attachments')->first();
            return response(['status' => 'success','post'=>$post],200);

        } catch (\Exception $e) {
            
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }

    }

    public function attachmentsDelete(Request $request, $id)
    {
        $data = $request->except('_token');
        try {
       
        $attachment = Attachment::find($id);
        $attachment->delete();

            return response(['status' => 'success','msg'=>"Images deleted successfully"],200);
        
        } catch (\Exception $e) {
            return response(['status' => 'error','msg'=>$e->getMessage()],401);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Post::where('UniqueId', $id)->first();
            $product->delete();
            return response(['status' => 'success','msg'=>'post deleted successfully'],200);
         } catch (\Exception $e) {
             return response(['status' => 'error','msg'=> $e->getMessage()],401);

         } 
    }
}
