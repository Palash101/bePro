<?php

namespace Modules\Creator\App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Creator\App\Models\BankDetails;

class CreatorController extends Controller
{
    public function addBankDetails(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string',
            'account_name' => 'required|string',
            'ifsc_code' => 'required|string',
            'account_number' => 'required|string',
        ]);

        $data = $request->except('_token');

        try {
        $user = auth()->user();
        $data['user_id'] = $user->id;
        BankDetails::create($data);
        return response(['status' => 'success','msg'=>"Bank details created successfully"],200);
        
        } catch (\Exception $e) {
           
            return response(['status' => 'error','msg'=>$e->getMessage()],401);

        }
    }
}
