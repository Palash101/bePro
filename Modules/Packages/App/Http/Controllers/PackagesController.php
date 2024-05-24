<?php

namespace Modules\Packages\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Packages\App\Models\Packages;
use App\Http\Traits\FileUpload;
class PackagesController extends Controller
{
    use FileUpload;

    public function getPackage(Request $request)
    {
        $user = auth()->user();
        $packages = Packages::whereUserId($user->id)->orderBy('created_at','desc')->get();
        return response(['status' => 'success','packages'=>$packages],200);
    }

    public function addPackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'discount' => 'nullable|string',
            'image' => 'required',
            'amount' => 'required|integer',
            'type' => 'required|in:Monthly,Yearly',
            'status' => 'required|in:Active,Blocked,Draft',
        ]);

        $data = $request->except('_token');

        try {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        $data['date'] = \Carbon\Carbon::today();

        if ($request->hasFile('image')) {
            $pathToUpload = 'uploads/package/';
            $file = $request->file('image');
            $data['image'] = $this->uploadFile($pathToUpload, $file);
        }
        Packages::create($data);
        return response(['status' => 'success','msg'=>"Package created successfully"],200);
        

        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }

   
}
