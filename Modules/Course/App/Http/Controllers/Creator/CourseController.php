<?php

namespace Modules\Course\App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\App\Models\Course;
use App\Models\User;
class CourseController extends Controller
{
    
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
	try{
        $user = auth()->user();
        $courses = Course::with('category')->whereCreatorId($user->id)->orderBy('created_at','DESC')->get();
     
        return response(['status' => 'success','courses'=>$courses ],200);
	} catch (\Exception $e) {
        
        return response(['status' => 'error',$e->getMessage()],401);

	}
    }

    public function activeCategory()
    {
	try{
        $user = auth()->user();
        $courses = Course::whereCreatorId($user->id)->where('status','Active')->orderBy('created_at','DESC')->get();
     
        return response(['status' => 'success','courses'=>$courses],200);
	} catch (\Exception $e) {
        
        return response(['status' => 'error',$e->getMessage()],401);

	}
    }
    

    public function getCoursebyDomain(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|exists:users,subdomain',
        ]);
        $checkSubdomain = User::where('subdomain',$request->domain)->first();
       
        $courses = Course::where('status','Active')->whereCreatorId($checkSubdomain->id)->orderBy('created_at','desc')->get();
        return response(['status' => 'success','courses'=>$courses],200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
         $request->validate([
            'title' => 'required|string',
            'featured_image' => 'required|string',
            'category_id' => 'required|numeric|exists:co_categories,id',
            'discount' => 'required|string',
            'payment_type' => 'required|string',
            'status' => 'required|string',
       
           
        ]); 
        $data = $request->except(['_token']);
        $user = auth()->user();
        
        $data['creator_id'] = $user->id;
        Course::create($data);

        
        return response(['status' =>'success','msg'=>'Course created successfully.'],200);
    }

    
    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = auth()->user();
        $categories = Course::whereCreatorId($user->id)->find($id);
        return response (['status'=>'success','categories'=>$categories],200);
    }

    


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
          //  'descriptions' => 'nullable|string',
        ]); 
        
         try {
            $data = $request->except(['_token']);
            $user = auth()->user();
            $categories = Course::whereCreatorId($user->id)->findOrFail($id);

            
 

            $categories->update($data);    

            return response(['status' => 'success','categories'=>$categories],200);
          

        } catch (\Exception $e) {
            return response(['status' => 'error','msg'=> $e->getMessage()],200);
            
          
        }

        return redirect()->back();
    }

     /**
     * Display a listing of the deleted resource.
     * @return Response
     */

    

        /**
             * Delete the specified resource in storage.
             * @param Request $request
             * @param int $id
             * @return Response
             */
    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $categories = Course::whereCreatorId($user->id)->findOrFail($id);
            $categories->delete();
            return response(['status' => 'success','msg'=>'Category deleted successfully'],200);
         } catch (\Exception $e) {
             return response(['status' => 'error','msg'=> $e->getMessage()],401);

         } 
    }

    public function changeStatus(Request $request,$id)
    {
        try {
            $user = auth()->user();
            $category = Course::whereCreatorId($user->id)->findOrFail($id);
            $category->status = $request->status;
            $category->save();   
            return response(['status' => 'success','msg'=>"Category $category->name status changed to $category->status successfully"],200);

        } catch (\Exception $e) {
            return response(['status' => 'error','msg'=>$e->getMessage()],401);
        }
    }
}
