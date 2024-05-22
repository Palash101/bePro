<?php
namespace App\Http\Traits;
use Illuminate\Support\Str;

trait FileUpload {

	//Upload Image File 
    public function uploadFile($pathToUpload,$file) {
      $fileName = Str::random(16) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path().'/'.$pathToUpload, $fileName);
        $filePath = $pathToUpload . $fileName; 
        return $filePath; 
    }

}
