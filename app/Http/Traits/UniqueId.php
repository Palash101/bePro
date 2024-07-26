<?php
namespace App\Http\Traits;
use Illuminate\Support\Str;

trait UniqueId {

	public function generateUniqueId() 
    {
        $uniqueNumber = Str::uuid();
        return $uniqueNumber;

      
    }

}
