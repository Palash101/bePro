<?php

namespace Modules\Course\App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Course\App\Models\Chapter;
class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request,$id)
    {
	try{
        $user = auth()->user();
        $chapters = Chapter::with('course')->whereCreatorId($user->id)->whereCourseId($id)->orderBy('created_at','DESC')->get();
     
        return response(['status' => 'success','chapters'=>$chapters ],200);
	} catch (\Exception $e) {
        
        return response(['status' => 'error',$e->getMessage()],401);

	}
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
            'course_id' => 'required|numeric|exists:co_courses,id',       
        ]); 
        $data = $request->except(['_token']);
        $user = auth()->user();
        
        $data['creator_id'] = $user->id;
        Chapter::create($data);

        
        return response(['status' =>'success','msg'=>'Chapter created successfully.'],200);
    }

    
    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = auth()->user();
        $chapters = Chapter::whereCreatorId($user->id)->find($id);
        return response (['status'=>'success','chapters'=>$chapters],200);
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
            'course_id' => 'required|numeric|exists:co_courses,id',
          //  'descriptions' => 'nullable|string',
        ]); 
        
         try {
            $data = $request->except(['_token']);
            $user = auth()->user();
            $chapters = Chapter::whereCreatorId($user->id)->findOrFail($id);

            
 

            $chapters->update($data);    

            return response(['status' => 'success','chapters'=>$chapters],200);
          

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
            $chapters = Chapter::whereCreatorId($user->id)->findOrFail($id);
            $chapters->delete();
            return response(['status' => 'success','msg'=>'Chapter deleted successfully'],200);
         } catch (\Exception $e) {
             return response(['status' => 'error','msg'=> $e->getMessage()],401);

         } 
    }

    
}
