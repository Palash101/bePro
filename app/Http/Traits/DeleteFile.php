<?php
namespace App\Http\Traits;


trait DeleteFile {

	 
    public function deleteFile($path) {
        
    	if(file_exists(base_path().'/'.$path))
           unlink(base_path().'/'.$path);

    }

}

