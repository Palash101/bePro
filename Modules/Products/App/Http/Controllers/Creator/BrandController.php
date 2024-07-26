<?php

namespace Modules\Products\App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Products\App\Models\Brand;
use App\Http\Traits\FileUpload;
use App\Http\Traits\UniqueId;
class BrandController extends Controller
{
    use FileUpload,UniqueId;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
	try{
        $user = auth()->user();
        $brands = Brand::whereCreatorId($user->id)->orderBy('created_at','DESC')->get();
     
        return response(['status' => 'success','brands'=>$brands ],200);
	} catch (\Exception $e) {
        
        return response(['status' => 'error',$e->getMessage()],401);

	}
    }

    public function activeBrand()
    {
	try{
        $user = auth()->user();
        $brands = Brand::whereCreatorId($user->id)->where('status','Active')->orderBy('created_at','DESC')->get();
     
        return response(['status' => 'success','brands'=>$brands],200);
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
            'name' => 'required|string',
          //  'descriptions' => 'nullable|string',
            'image' => 'required',
           
        ]); 
        
        $data = $request->except(['_token']);
        $unique =  $this->generateUniqueId();
        
        $user = auth()->user();
        if($request->hasFile('image'))
        { 
        $pathToUpload = 'images/brands/';
        $file = $request->file('image');
        $data['image'] = $this->uploadFile($pathToUpload,$file);
        }
        $data['creator_id'] = $user->id;
        $data['UniqueId'] = $unique;
        Brand::create($data);

        
        return response(['status' =>'success','msg'=>'Brands created successfully.'],200);
    }

    
    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $brands = Brand::whereCreatorId($user->id)->where('UniqueId', $id)->first();
        return response (['status'=>'success','brands'=>$brands],200);
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
            'name' => 'required|string',
          //  'descriptions' => 'nullable|string',
        ]); 
        
         try {
            $data = $request->except(['_token']);
            $user = auth()->user();
            $brands = Brand::whereCreatorId($user->id)->where('UniqueId', $id)->first();

            if($request->hasFile('image'))
            { 
            $pathToUpload = 'images/brands/';
            $file = $request->file('image');
            $data['image'] = $this->uploadFile($pathToUpload,$file);
            };
 

            $brands->update($data);    

            return response(['status' => 'success','brands'=>$brands],200);
          

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
            $brands = Brand::whereCreatorId($user->id)->where('UniqueId', $id)->first();
            $brands->delete();
            return response(['status' => 'success','msg'=>'Brands deleted successfully'],200);
         } catch (\Exception $e) {
             return response(['status' => 'error','msg'=> $e->getMessage()],401);

         } 
    }

    public function changeStatus($id)
    {
        try {
            $user = auth()->user();
            $brand = Brand::whereCreatorId($user->id)->where('UniqueId', $id)->first();
            if($brand->status == 'Active'){
                $brand->status = 'Block';
            }else{
                $brand->status = 'Active';
            }
            $brand->save();   
            return response(['status' => 'success','msg'=>"Brand $brand->name status changed to $brand->status successfully"],200);

        } catch (\Exception $e) {
            return response(['status' => 'error','msg'=>$e->getMessage()],401);
        }
    }
}
