<?php

namespace Modules\Theme\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Theme\App\Models\Theme;
class ThemeController extends Controller
{
    public function getThemes(Request $request)
    {
        $themes = Theme::orderBy('created_at','desc')->get();
        return response(['status' => 'success','themes'=>$themes],200);
    }

   
}
