<?php
namespace App\Http\Traits;
use Illuminate\Support\Str;

trait Domain {

	public function subDomainCheck($encodedData) 
    {
      
               $curl = curl_init();      
                curl_setopt_array($curl, array(
                CURLOPT_URL => "https://vercel.com/api/v10/projects/prj_Z15KgHLes0OHKlOXCOiODnmmjOTy/domains?teamId=team_dKrMGxUd5uIEy4jhW1y7gdFa",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>$encodedData,
                CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer xXvvZpN8EMUVpWBf1RZ4jQHC'
                ),
            ));
            
            $response = curl_exec($curl);          
            curl_close($curl);
      
    }

}
