<?php

namespace Modules\Products\App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Products\App\Models\Product;
use Modules\Products\App\Models\Variant;

class ProductController extends Controller
{
    use FileUpload;
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        // $value = $request->value;
        // $limit = $request->limit;
        // $orderBy = $request->orderBy;
        // $price = $request->price;
        // $stock = $request->stock;
        // $stockValue = null; 
        // $priceValue = null; 
        // if($stock){
        //    if($stock == 'High to low') {
        //     $stockValue = 'desc';
        //    }else{
        //     $stockValue = 'asc';
        //    }
        // }

        // if($price){
        //     if($price == 'High to low') {
        //      $priceValue = 'desc';
        //     }else{
        //      $priceValue = 'asc';
        //     }
        //  }


        // $products = Product::with(['images','ingredients','productCategories','bodyPartCategories']);
        // if ($request->has('value')) {
        //     $products->orWhere('name', 'like', "%{$value}%");
        //     $products->orWhereHas('productCategories', function ($query) use ($value) {
        //        $query->where('name', 'like', "%{$value}%");
        //    });
        //    $products->orWhereHas('bodyPartCategories.subcategories', function ($query) use ($value) {
        //        $query->where('name', 'like', "%{$value}%");
        //    });
        // }
        // if ($request->has('stock')) {
        //     $products = $products->orderBy('current_stock',$stockValue);
        // }
        // if ($request->has('price')) {
        //     $products = $products->orderBy('price',$priceValue);
        // }
        // if ($orderBy) {
        //     $products = $products->orderBy('id',$orderBy);
        // }
        // $products = $products->whereNull('variant_type')->orderBy('id','desc')->get();
        $user = auth()->user();
        $products = Product::whereCreatorId($user->id)->orderBy('created_at','DESC')->get();
        return response(['status' => 'success','products'=>$products],200);
    }
    


    public function active(Request $request)
    {
       
        $products = Product::whereStatus('Active');
          
       $products =   $products->orderBy('created_at','desc')->get();

        return response(['status' => 'success','products'=>$products],200);
    }

 


    public function store(Request $request)
    {
       

        $request->validate([
            'name' => 'required|string',
            'product_category_id' => 'required|numeric|exists:categories,id',
            'body_parts_category_id' => 'required|numeric|exists:categories,id',
            'status' => 'required|in:Active,Blocked,Draft',
            'discount' => 'nullable|in:true,false',
            'trending' => 'required|in:true,false',
            'auth' => 'required|in:true,false',
            'type' => 'nullable|in:Quiz,Code',
            'description' => 'nullable|string',
            'subcategories' => 'nullable|string',
            'price' => 'nullable|integer',
            'discount_price' => 'nullable|integer|required_if:discount,true',
            'current_stock' => 'nullable|integer',
            'upcoming_stock' => 'nullable|integer',
            'position' => 'nullable|integer',
        ]);

        $data = $request->except('_token');

        try {
            $number = random_int(10000, 99999);
            $code = '';
            if($request->type == 'Code') {
                $code = $number;
            }   
            $GroupId = uniqid();
           
            
            $data['featured_image'] = '';
            if($request->hasFile('featuredImage'))
            { 
            $pathToUpload = 'images/product/';
            $file = $request->file('featuredImage');
            $data['featured_image'] = $this->uploadFile($pathToUpload,$file);
            }

        if(!empty(($request->variants))){
            foreach(($request->variants) as $variant){ 
                $variant_image = '';
                if(!empty($variant['variant_image']))
                { 
                $pathToUpload = 'images/product/';
                $file = $variant['variant_image'];
                $variant_image = $this->uploadFile($pathToUpload,$file);
                }
                $discount = 'false';
                if(!empty($variant['variant_discount_price'])){
                    $discount = 'true';
                }


                              
                $products = new Products;
                $products->variant_type = $request->variant_type;
                $products->name = $request->name;
                $products->description = (!empty($request['description'])) ? $request['description'] : null;;
                $products->product_category_id = $request->product_category_id;
                $products->body_parts_category_id = $request->body_parts_category_id;
                $products->auth = $request->auth;
                $products->trending = $request->trending;
                $products->type = (!empty($request['type'])) ? $request['type'] : null;;
                $products->discount = $discount;
                $products->discount_price = (!empty($variant['variant_discount_price'])) ? $variant['variant_discount_price'] : null;
                $products->variant_text = (!empty($variant['variant_text'])) ? $variant['variant_text'] : null;
                $products->subcategories = (!empty($request['subcategories'])) ? $request['subcategories'] : null;
                $products->body_parts_category_id = $request->body_parts_category_id;
                $products->brand_id = $request->brand_id;
                $products->variant_name = $variant['variant_name'];
                $products->upcoming_stock = $request->upcoming_stock;
                $products->position = $request->position;
                $products->current_stock = $variant['variant_stock'];
                $products->status = $request->status;
                $products->price = $variant['variant_price'];
                $products->featured_image = $data['featured_image'];
                $products->variant_image = $variant_image;
                $products->code = $code;
                $products->GroupId = $GroupId;
              // dd($products);
                $products->save();

                $text = 'Variant product new added quantity';
                $this->addStockHistory($products->id,'VariantAdded',$products->id,0,0,$variant['variant_stock'],'Credit',$text);
                
                if(!empty(($variant['variant_banner_images']))){
                    foreach($variant['variant_banner_images'] as $image){
                        $pathToUpload = 'images/product/';
                        $variantImage = $this->uploadFile($pathToUpload, $image, 100, 100);
                        $imageSave = new Image;
                        $imageSave->parent_id = $products->id;
                        $imageSave->image =  $variantImage;
                        $imageSave->type = 'Product';                      
                        $imageSave->save();
                    }
                }
               
                if(!empty(json_decode($request->ingredients))){
                    foreach(json_decode($request->ingredients) as $ingredient){
                        $ingredientSave = new ProductIngredients;
                        $ingredientSave->product_id = $products->id;
                        $ingredientSave->ingredients_id = $ingredient;
                        $ingredientSave->save();
                    }
                }
                
            }

            return response(['status' => 'success','msg'=>"Product created successfully"],200);
        }else{
          
            $number = random_int(10000, 99999);
        
            if($request->type == 'Code') {
                $data['code'] = $number;
            }   
    
            $data['GroupId'] = $GroupId;
            
            $product =  Product::create($data);
            $text = 'Product new added quantity';
            $this->addStockHistory($product->id,'ProductAdded',$product->id,0,0,$request['current_stock'],'Credit',$text);

       if(!empty(json_decode($request->ingredients))){
        foreach(json_decode($request->ingredients) as $ingredient){
            $image = new ProductIngredients;
            $image->product_id = $product->id;
            $image->ingredients_id = $ingredient;
            $image->save();
        }
    }
        }
       
       if($request->hasFile('images')){
        foreach($request->file('images') as $image){
            $pathToUpload = 'images/product/';
            $data['image'] = $this->uploadFile($pathToUpload, $image, 100, 100);
            $image = new Image;
            $image->parent_id = $product->id;
            $image->image = $data['image'];
            $image->type = 'Product';
            $image->save();
        }
    }
       


        return response(['status' => 'success','msg'=>"Product created successfully"],200);
        

        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }

    public function Purchase(Request $request)
    {
     
        $request->validate([            
            'vendor_id' => 'required|numeric|exists:vendors,id',
            'currency' => 'required|in:EUR,QAR,USD,AUD,KWD',
            'order_date' => 'required',
            'payment_status' => 'required|in:Paid,Not Paid,Partial Paid',
        ]);



          try{
        $data = $request->except('_token');
        $data['status'] = 'Pending';
        $data['date'] = $request->order_date;
        $purchase =  Purchase::create($data);
        $totalCost = 0;
        $totalQuantity = 0;

        foreach(json_decode($request->products) as $data){
         
            $tax = 0;
            if(!empty($data->tax)){
                $tax = $data->tax;
            }
           
            $price= ($data->price_per_item*$data->quantity);
           
            $taxRate=$tax;
            $taxAmount=$price*$taxRate/100;
            $total=$price+$taxAmount;
            
           

            $cartItems = [
             'product_purchase_id' => $purchase->id,
             'product_id' => $data->product_id,
             'quantity' => $data->quantity,
             'price_per_item' => $data->price_per_item,
             'tax' => $tax,
             'total' => $total,
             ];
             $product = PurchaseItem::create($cartItems);
             $totalCost += $total;
             $totalQuantity += $data->quantity;            
         }
         $purchase->total = $totalCost;
         $purchase->totalQuantity = $totalQuantity;
         $purchase->save();



        return response(['status' => 'success','msg'=>"Product purchased successfully"],200);

        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }

    public function show($id)
    {
        try {
            
            $product = Product::with(['images','ingredients'])->find($id);
            return response(['status' => 'success','product'=>$product],200);

        } catch (\Exception $e) {
            
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }

    }

    public function changeStatus(Request $request,$id)
    {
        try {
            
            $category = Product::findOrFail($id);
            $category->status = $request->status;
            $category->save();   
            return response(['status' => 'success','msg'=>"Category $category->name status changed to $category->status_text successfully"],200);

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
            'product_category_id' => 'required|numeric|exists:categories,id',
            'body_parts_category_id' => 'required|numeric|exists:categories,id',
            'status' => 'required|in:Active,Blocked,Draft',
            'discount' => 'required|in:true,false',
            'trending' => 'required|in:true,false',
            'auth' => 'required|in:true,false',
            'type' => 'nullable|in:Quiz,Code',
            'description' => 'nullable|string',
            'subcategories' => 'nullable|string',
            'price' => 'required|integer',
            'discount_price' => 'nullable|integer|required_if:discount,true',
            'current_stock' => 'required|integer',
            'upcoming_stock' => 'nullable|integer',
            'position' => 'nullable|integer',

        ]);

        


        $data = $request->except(['_token']); 

        try {
            $product = Product::findOrFail($id);
            $oldProduct = Product::findOrFail($id);
            $number = random_int(10000, 99999);
            if($request->type == 'Code') {
                $data['code'] = $number;
            } 

            if($request->hasFile('variant_image'))
            { 
            $pathToUpload = 'images/product/';
            $file = $request->file('variant_image');
            $data['variant_image'] = $this->uploadFile($pathToUpload,$file);
            }
            
            if($request->hasFile('featuredImage'))
            { 
            $pathToUpload = 'images/product/';
            $file = $request->file('featuredImage');
            $data['featured_image'] = $this->uploadFile($pathToUpload,$file);
            }

            $product->update($data);  
            if($request->current_stock){
                $text = 'stock has been added in the admin update stock function';
                $this->addStockHistory($product->id,'ProductUpdate',$product->id,$oldProduct->current_stock,$product->current_stock,$request->current_stock,'Credit',$text);
            }
            
           

           
            
            if(!empty(json_decode($request->ingredients))){
                foreach(json_decode($request->ingredients) as $ingredient){
                    $image = new ProductIngredients;
                    $image->product_id = $product->id;
                    $image->ingredients_id = $ingredient;
                    $image->save();
                }
            }
               
               if($request->hasFile('images')){
                foreach($request->file('images') as $image){
                    $pathToUpload = 'images/product/';
                    $data['image'] = $this->uploadFile($pathToUpload, $image, 100, 100);
                    $image = new Image;
                    $image->parent_id = $product->id;
                    $image->image = $data['image'];
                    $image->type = 'Product';
                    $image->save();
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
            $product = Product::findOrFail($id);
            $product->delete();
            return response(['status' => 'success','msg'=>'product deleted successfully'],200);
         } catch (\Exception $e) {
             return response(['status' => 'error','msg'=> $e->getMessage()],401);

         } 
    }

    public function purchasePackage(Request $request)
    {
        $purchase = Purchase::with(['items.product:id,name,variant_name','vendor'])->orderBy('created_at','desc')->get();
        return response(['status' => 'success','purchase'=>$purchase],200);

    }

    public function purchaseShow($id)
    {
        $purchase = Purchase::with(['items.product:id,name,variant_name','vendor','notCompletedItems.product:id,name,variant_name'])->orderBy('created_at','desc')->find($id);
        return response(['status' => 'success','purchase'=>$purchase],200);

    }

    public function updatePackagePurchaseQuantity(Request $request,$id)
    {
       
        try{
        $data = $request->except('_token');
     
        $purchase = Purchase::findOrFail($id);
        if ($purchase->status == 'Received') {
            return response(['status' => 'success','msg'=>"This order already Received"],400);
        }
        $purchase->status = 'Partially Fullfiled';
        $purchase->save();
        

        foreach(json_decode($request->products) as $purchaseData){
            
            $item = PurchaseItem::find($purchaseData->item_id);
           
            $productName = Product::find($item->product_id);
           
            $requestReceivedQty = 0;
            $requestRejectQty = 0;
            if(!empty($purchaseData->received_qty)){
                $notifyMe = ProductNotify::where('products_id',$productName->id)->get();
                if(!empty($notifyMe)){
                    $ids = [];                    
                    $title = 'Notify Me';
                    $message = "Exciting news! The ".$productName->name." you've been waiting for is back in stock. Grab it now before it's gone again!";
                    
                    foreach($notifyMe as $notify){                        
                        $ids[] = $notify->user_id."";
                    }
                   

                  $this->sendFCMNotification($title, $message, $ids);
                  $this->notificationsbyUser($title,$message,$ids);
                }
               // $notifyMe = ProductNotify::where('products_id',$productName->id)->delete();
                            
              $requestReceivedQty =   $purchaseData->received_qty;
              
            }
            if(!empty($purchaseData->rejected_qty)){
                $requestRejectQty = $purchaseData->rejected_qty;
            }
            
            $products = Product::find($item->product_id);
            $acceptQuanitity = ($item->accept + $item->reject+ $requestReceivedQty + $requestRejectQty);
            $onlyAcceptQuanitity = ($item->accept + $requestReceivedQty);

            if ($onlyAcceptQuanitity > $item->quantity) {
                return response(['status' => 'success','msg'=>"Accepted quantity cannot be greater than total quantity"],401);
            }
            $history = new PurchaseHistory;
            $history->product_purchase_id = $item->product_purchase_id;
            $history->product_id = $item->product_id;
            $history->reject = $requestRejectQty;
            $history->accept = $requestReceivedQty;
            $history->quantity = $item->quantity;
            $history->save();

            

            
            $status = 'Not Completed';

            if ($onlyAcceptQuanitity >= $item->quantity) {
                $status = 'Completed';
            }

           
            $cartItems = [
             'accept' => $item->accept+$requestReceivedQty,
             'reject' => $item->reject+$requestRejectQty,
             'status' => $status,
             ];

             
             $item->update($cartItems);
            
            

            

             if($requestReceivedQty){
                $product = Product::find($item->product_id);
                $oldProduct = Product::find($item->product_id);
                $product->current_stock = $product->current_stock+$requestReceivedQty;
                $product->save();
                $text = 'purchase product add quantity';
                $this->addStockHistory($product->id,'PurchaseProduct',$purchase->id,$oldProduct->current_stock,$product->current_stock,$requestReceivedQty,'Credit',$text);
             }
            
            $purchaseQuantity = Purchase::findOrFail($id);
            $purchaseQuantity->acceptQuantity = $purchaseQuantity->acceptQuantity+$requestReceivedQty;            
            $purchaseQuantity->save();

         }

         $purchaseNew = Purchase::findOrFail($id);
        
        if ($purchaseNew->acceptQuantity >= $purchaseNew->totalQuantity) {
            $purchaseNew->status = 'Received';            
        }     
        
        $purchaseNew->save();
         
        
        return response(['status' => 'success','msg'=>"Product updated successfully"],200);

        } catch (\Exception $e) {
           dd($e);
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }

    public function updatePackagePurchase(Request $request,$id)
    {
       
        $request->validate([            
            'vendor_id' => 'required|numeric|exists:vendors,id',
            'currency' => 'required|in:EUR,QAR,USD,AUD,KWD',
            'order_date' => 'required',
            'payment_status' => 'required|in:Paid,Not Paid,Partial Paid',
            'status' => 'required|in:Cancelled,Partially Fullfiled,Received,Pending',
        ]);
        

        
        try{
        $data = $request->except('_token');
     
        $purchase = Purchase::findOrFail($id);

        // if ($purchase->status != 'Pending' || $purchase->status != 'Cancelled') {
        //     return response(['status' => 'success','msg'=>"Product cannot be updated. Status is not pending."],400);
        // }
        $data['date'] = $request->order_date;
        $purchase->update($data);      
        $totalCost = 0;
        $totalQuantity = 0;
        $ids = [];

        foreach(json_decode($request->products) as $purchaseData){
            $item = null;
            if(!empty($purchaseData->item_id)){
                $item = PurchaseItem::find($purchaseData->item_id);
            }
            $tax = 0;
            if(!empty($purchaseData->tax)){
                $tax = $purchaseData->tax;
            }

            $price= ($purchaseData->price_per_item*$purchaseData->quantity);
           
            $taxRate=$tax;
            $taxAmount=$price*$taxRate/100;
            $total=$price+$taxAmount;
          
           
            $cartItems = [
             'product_purchase_id' => $purchase->id,
             'product_id' => $purchaseData->product_id,
             'quantity' => $purchaseData->quantity,
             'price_per_item' => $purchaseData->price_per_item,
             'tax' => $tax,
             'total' => $total,
             ];
                if (is_null($item)) {
                    PurchaseItem::create($cartItems);
                } else {
                    $item->update($cartItems);
                }
             
             if($data['status'] == 'Received'){
                $product = Product::find($purchaseData->product_id);
                $product->current_stock = $product->current_stock+$purchaseData->quantity;
                $product->save();

             }
             $totalCost += $total;  
             $totalQuantity += $purchaseData->quantity;            
         }
         $purchase->total = $totalCost;
         $purchase->totalQuantity = $totalQuantity;
         $purchase->save();
         


        return response(['status' => 'success','msg'=>"Product updated successfully"],200);

        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }

    

    public function purchaseHistory($id)
    {
        try {
            $purchase = Purchase::with(['history.product','product','vendor'])->find($id);
            
            return response(['status' => 'success','purchase'=>$purchase],200);
         } catch (\Exception $e) {
             return response(['status' => 'error','msg'=> $e->getMessage()],401);

         } 
    }

    public function productCodeGenerate(Request $request)
    {
       
        $request->validate([            
            'user_id' => 'required|numeric|exists:users,id',
        ]);
        


        $data = $request->except(['_token']); 
       $code = [];
        try {
          
            if(!empty(json_decode($request->product_id))){
                foreach(json_decode($request->product_id) as $product){
                    $number = random_int(10000, 99999);
                    $productId = Product::find($product);
                    $productCode = new ProductsCode;
                    $productCode->code = $number;
                    $code[] = $number;
                   
                    $productCode->product_id = $productId->id;
                    $productCode->user_id = $request->user_id;
                    $productCode->save();
                }
            }
            $data = ProductsCode::whereIn('code',$code)->select('id','code','product_id')->with('products:id,name')->get();
         
            return response(['status' => 'success','msg'=>"Product Code generated successfully",'data'=> $data],200);
          

        } catch (\Exception $e) {
            
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }

    }

    public function variantUpdate(Request $request,$id)
    {
       

        

        $data = $request->except('_token');
        $product = Product::where('GroupId',$id)->first();
        try {
            $number = random_int(10000, 99999);
            $code = '';
            if($request->type == 'Code') {
                $code = $number;
            }   
          
        

        if(!empty(($request->variants))){
            foreach(($request->variants) as $variant){    
                     
                if(!empty($variant['variant_id'])){

                    $variantOldData = Product::where('id', $variant['variant_id'])->first();
                    $variant_image = $variantOldData['variant_image'];;
                    if(!empty($variant['variant_image']))
                    { 
                    $pathToUpload = 'images/product/';
                    $file = $variant['variant_image'];
                    $variant_image = $this->uploadFile($pathToUpload,$file);
                    }

                    $discountPrice = $variantOldData['variant_discount_price'];
                    $discountData = false;
                    if(!empty($variant['variant_discount_price'])){
                        $discountPrice = $variant['variant_discount_price'];
                        $discountData = true;
                    }
                   // dd($variant['variant_text']);
                    $varianTextData = $variantOldData['variant_text'];
                    if($variant['variant_text'] == 'null'){                       
                        $varianTextData = null;
                    }elseif(!empty($variant['variant_text'])){
                        $varianTextData = $variant['variant_text'];
                    }
                  
                    $productData = Product::where('id', $variant['variant_id'])->first();
                    $productData->variant_text = $varianTextData;
                    $productData->variant_image = $variant_image;
                    $productData->variant_name = $variant['variant_name'];
                    $productData->description = $request['description'];
                    $productData->name = $request['name'];
                    $productData->status = $request['status'];
                    $productData->trending = $request['trending'];

                    $productData->price = $variant['variant_price'];
                    // $productData->discount_price = $discountPrice;
                    // $productData->discount = $discountData;
                    $productData->current_stock = $variant['variant_stock'];
                    $productData->variant_image = $variant_image;;                
                    $productData->save();

                    if ($variantOldData->current_stock < (int)$productData->current_stock) {
                        $text = 'Variant Product update quantity';
                    $this->addStockHistory($productData->id,'VariantUpdate',$productData->id,$variantOldData->current_stock,$productData->current_stock,$variant['variant_stock'],'Credit',$text);
                    }
                   
                   


                    if(!empty(($variant['variant_banner_images']))){
                        foreach($variant['variant_banner_images'] as $image){
                            $pathToUpload = 'images/product/';
                            $variantImage = $this->uploadFile($pathToUpload, $image, 100, 100);
                            $imageSave = new Image;
                            $imageSave->parent_id = $variant['variant_id'];
                            $imageSave->image =  $variantImage;
                            $imageSave->type = 'Product';                      
                            $imageSave->save();
                        }
                    }
                    
                }else{
                    $variant_image = '';
                    if(!empty($variant['variant_image']))
                    { 
                    $pathToUpload = 'images/product/';
                    $file = $variant['variant_image'];
                    $variant_image = $this->uploadFile($pathToUpload,$file);
                    }
                    $discount = 'false';
                    if(!empty($variant['variant_discount_price'])){
                        $discount = 'true';
                    }
                    
                    $products = new Products;
                    $products->variant_type = $request->variant_type;
                    $products->name = $request->name;
                    $products->description = (!empty($request['description'])) ? $request['description'] : null;;
                    $products->product_category_id = $product->product_category_id;
                    $products->body_parts_category_id = $product->body_parts_category_id;
                    $products->auth = $product->auth;
                    $products->trending = $request->trending;
                    $products->type = (!empty($product['type'])) ? $product['type'] : null;;
                    $products->discount = $discount;
                    $products->discount_price = (!empty($variant['variant_discount_price'])) ? $variant['variant_discount_price'] : null;
                    $products->subcategories = (!empty($product['subcategories'])) ? $product['subcategories'] : null;
                    $products->body_parts_category_id = $product->body_parts_category_id;
                    $products->brand_id = $request->brand_id;
                    $products->variant_name = $variant['variant_name'];
                    $products->upcoming_stock = $product->upcoming_stock;
                    $products->position = $product->position;
                    $products->current_stock = $variant['variant_stock'];
                    $products->status = $request->status;
                    $products->price = $variant['variant_price'];
                    $products->featured_image = $product['featured_image'];
                    $products->variant_image = $variant_image;
                    $products->code = $code;
                    $products->GroupId = $product->GroupId;               
                    $products->save();
                    $text = 'Product new added quantity';
                    $this->addStockHistory($products->id,'VariantAdded',$products->id,0,0,$variant['variant_stock'],'Credit',$text);
                    
                    if(!empty(($variant['variant_banner_images']))){
                        foreach($variant['variant_banner_images'] as $image){
                            $pathToUpload = 'images/product/';
                            $variantImage = $this->uploadFile($pathToUpload, $image, 100, 100);
                            $imageSave = new Image;
                            $imageSave->parent_id = $products->id;
                            $imageSave->image =  $variantImage;
                            $imageSave->type = 'Product';                      
                            $imageSave->save();
                        }
                    }
                }


                
               
                
            }
        }

            return response(['status' => 'success','msg'=>"Product updated successfully"],200);        

        } catch (\Exception $e) {
          
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }
}
