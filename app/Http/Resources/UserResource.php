<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   public function toArray($request)
    {
  
        return [
            'name' => $this->name,
            'username' => $this->username,
            'profile' => $this->profile,
            'email' => $this->email,
            'banner' => $this->banner,
            'dob' => $this->dob,
            'id' => $this->id,
            'phone' => $this->phone,
        ];
    }

}
