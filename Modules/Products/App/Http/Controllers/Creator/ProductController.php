<?php

namespace Modules\Products\App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Products\App\Models\Product;
use Modules\Products\App\Models\Variant;
use App\Http\Traits\FileUpload;
use Illuminate\Support\Str;
use App\Models\User;
use App\Http\Traits\UniqueId;
class ProductController extends Controller
{
    use FileUpload,UniqueId;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
       
        $user = auth()->user();
        $products = Product::with(['brand:id,name','category:id,name'])->with('variants:id,product_id,variant_name,images,banner_images,text,price,discount,discount_price,descriptions,current_stock,upcoming_stock,status,type')->whereCreatorId($user->id)->orderBy('created_at','DESC')->get();
        return response(['status' => 'success','products'=>$products],200);
    }

    public function getPostbyDomain(Request $request)
    {

        $request->validate([
            'domain' => 'required|string|exists:users,subdomain',
        ]);
     
        $checkSubdomain = User::where('subdomain',$request->domain)->first();
       
        $products = Product::with(['brand:id,name','category:id,name'])->with('variants:id,product_id,variant_name,images,banner_images,text,price,discount,discount_price,descriptions,current_stock,upcoming_stock,status,type')->whereCreatorId($checkSubdomain->id)->orderBy('created_at','desc')->get();
        return response(['status' => 'success','products'=>$products],200);
    }



    public function active(Request $request)
    {
        $user = auth()->user();
        $products = Product::whereCreatorId($user->id)->whereStatus('Active');
          
       $products =   $products->orderBy('created_at','desc')->get();

        return response(['status' => 'success','products'=>$products],200);
    }

 


    public function store(Request $request)
    {
      
        $request->validate([
            'name' => 'required|string',
            'featured_image' => 'required|string',
            'category_id' => 'required|numeric|exists:pro_categories,id',
            'brand_id' => 'required|numeric|exists:pro_brands,id',
            'status' => 'required|in:Active,Block,Draft',
            'discount' => 'nullable|in:true,false',        
            'price' => 'nullable|integer',
            'discount_price' => 'nullable|integer|required_if:discount,true',
            'current_stock' => 'nullable|integer',
            'stock' => 'nullable|integer',
        ]);

        $data = $request->except('_token');

        try {
            $user = auth()->user();
            $data['featured_image'] = $request->featured_image;
            $data['creator_id'] = $user->id;
            $data['slug'] = Str::slug($request->name);
            $data['type'] = $request->variant_type;
            $unique =  $this->generateUniqueId();
            $data['UniqueId'] = $unique;
            $product = Product::create($data);

        if(!empty(($request->variant_type))){
            if($request->variant_type == 'None'){ 
                $uniqueVariantId =  $this->generateUniqueId();
                $variant = new Variant;
                $variant->creator_id = $user->id;
                $variant->UniqueId = $uniqueVariantId;
                $variant->type = 'Normal';
                $variant->product_id = $product->id;
                $variant->price = $request->price;
                $variant->discount = $request->discount;
                $variant->discount_price = (!empty($request['discount_price'])) ? $request['discount_price'] : null;;
                $variant->descriptions = (!empty($variant['descriptions'])) ? $variant['descriptions'] : null;
                $variant->current_stock = $request->current_stock;
                $variant->upcoming_stock = $request->upcoming_stock;               
                $variant->save();          
            }else{
                
                $variantsData = json_decode($request->variants, true);                
                foreach(($variantsData) as $variant){ 
                    $uniqueVariantId =  $this->generateUniqueId();
                    $variantProduct = new Variant;
                    $variantProduct->creator_id = $user->id;
                    $variant->UniqueId = $uniqueVariantId;
                    $variantProduct->product_id = $product->id;
                    $variantProduct->variant_name = $variant['variant_name'];
                    $variantProduct->price = $variant['variant_price'];
                    $variantProduct->discount = true;
                    $variantProduct->type = $request['variant_type'];
                    $variantProduct->discount_price = (!empty($variant['variant_discount_price'])) ? $variant['variant_discount_price'] : null;;
                    $variantProduct->descriptions = (!empty($variant['descriptions'])) ? $variant['descriptions'] : null;
                    $variantProduct->current_stock = $variant['variant_stock'];
                    $variantProduct->text = $variant['variant_text'];
                    $variantProduct->banner_images = (!empty($variant['variant_banner_images'])) ? $variant['variant_banner_images'] : null;;
                    $variantProduct->images = (!empty($variant['variant_images'])) ? implode(',', $variant['variant_images']) : null;                 
                    $variantProduct->save();  
                    
                }     
            }
            return response(['status' => 'success','msg'=>"Product created successfully"],200);
        }
        

        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }

    

    public function show($id)
    {
        try {
            
            $product = Product::with('variants:id,product_id,variant_name,images,banner_images,text,price,discount,discount_price,descriptions,current_stock,upcoming_stock,status,type')->where('UniqueId', $id)->first();
            return response(['status' => 'success','product'=>$product],200);

        } catch (\Exception $e) {
            
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }

    }

    public function changeStatus(Request $request,$id)
    {
        try {
            
            $product = Product::where('UniqueId', $id)->first();
            $product->status = $request->status;
            $product->save();   
            return response(['status' => 'success','msg'=>"Product $product->name status changed to $product->status successfully"],200);

        } catch (\Exception $e) {
            
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }

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
            'featured_image' => 'required|string',
            'category_id' => 'required|numeric|exists:pro_categories,id',
            'status' => 'required|in:Active,Block,Draft',
            'discount' => 'nullable|in:true,false',        
            'price' => 'nullable|integer',
            'discount_price' => 'nullable|integer|required_if:discount,true',
            'current_stock' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'brand_id' => 'required|numeric|exists:pro_brands,id',
        ]);
        //dd($request);


        $data = $request->except(['_token']); 

        try {
            $product = Product::where('UniqueId', $id)->first();
            $data['featured_image'] = $request->featured_image;
            $product->update($data);  
            $variantsData = json_decode($request->variants, true); 
            if(!empty(($variantsData))){
                foreach(($variantsData) as $variant){    
                         
                    if(!empty($variant['id'])){                            
                        $variantData = Variant::where('UniqueId', $variant['id'])->first();                     
                        $variantData->variant_name = $variant['variant_name'];
                        $variantData->price = $variant['variant_price'];
                        $variantData->discount = true;
                        $variantData->type = $request['variant_type'];
                        $variantData->discount_price = (!empty($variant['variant_discount_price'])) ? $variant['variant_discount_price'] : null;;
                        $variantData->descriptions = (!empty($variant['descriptions'])) ? $variant['descriptions'] : null;
                        $variantData->current_stock = $variant['variant_stock'];
                        $variantData->text = $variant['variant_text'];
                        $variantData->banner_images = (!empty($variant['variant_banner_images'])) ? $variant['variant_banner_images'] : null;;
                        $variantData->images = (!empty($variant['variant_images'])) ? implode(',', $variant['variant_images']) : null;                 
                        $variantData->save();  

                    }else{
                        $user = auth()->user();
                        $unique =  $this->generateUniqueId();
                        $variantProduct = new Variant;
                        $variantProduct->creator_id = $user->id;
                        $variantProduct->UniqueId = $unique;
                        $variantProduct->product_id = $product->id;
                        $variantProduct->variant_name = $variant['variant_name'];
                        $variantProduct->price = $variant['variant_price'];
                        $variantProduct->discount = true;
                        $variantProduct->type = $request['variant_type'];
                        $variantProduct->discount_price = (!empty($variant['variant_discount_price'])) ? $variant['variant_discount_price'] : null;;
                        $variantProduct->descriptions = (!empty($variant['descriptions'])) ? $variant['descriptions'] : null;
                        $variantProduct->current_stock = $variant['variant_stock'];
                        $variantProduct->text = $variant['variant_text'];
                        $variantProduct->banner_images = (!empty($variant['variant_banner_images'])) ? $variant['variant_banner_images'] : null;;
                        $variantProduct->images = (!empty($variant['variant_images'])) ? implode(',', $variant['variant_images']) : null;                 
                        $variantProduct->save();  
                       
                    }
    
    
                    
                   
                    
                }
            }
            
            
            return response(['status' => 'success','msg'=>"Product updated successfully"],200);
          

        } catch (\Exception $e) {
            
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }



    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::where('UniqueId', $id)->first();
            $product->delete();
            return response(['status' => 'success','msg'=>'product deleted successfully'],200);
         } catch (\Exception $e) {
             return response(['status' => 'error','msg'=> $e->getMessage()],401);

         } 
    }


    public function variantDelete($id)
    {
        try {
            $variantDelete = Variant::where('UniqueId', $id)->first();
            $variantDelete->delete();
            return response(['status' => 'success','msg'=>'variant deleted successfully'],200);
         } catch (\Exception $e) {
             return response(['status' => 'error','msg'=> $e->getMessage()],401);

         } 
    }
}
