<?php 

function crul($ur,$data)
{
       $u="http://sandboxapi.medicalwale.com/v51/";
       $url=$u.$ur;
       $ch = curl_init($url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
       curl_setopt($ch, CURLOPT_HTTPHEADER,['User-ID:1','Authorizations:25iwFyq/LSO1U','Client-Service:frontend-client','Auth-Key:medicalwalerestapi']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       $result = curl_exec($ch);
       curl_close($ch);
       return $result;
}

/*function healthwall_adver_crul($ur,$data)
{
    
    $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ur);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            
            $httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE); // this results 0 every time
            $response = curl_exec($ch);
          
           if ($response === false) 
               $response = curl_error($ch);
            
          $str=stripslashes($response);
              
            curl_close($ch);
         
       return $str;
      
}*/
function healthwall_adver_crul($ur,$data)
{
    
      $ch = curl_init();
        
         curl_setopt($ch, CURLOPT_URL, $ur);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
          curl_setopt($ch, CURLOPT_TIMEOUT, 5);
          $data = curl_exec($ch);
          $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          curl_close($ch);
          if($httpcode>=200 && $httpcode<300) 
          {
                  $str=stripslashes($data);
          }
       else
       {
              $str="";
       }           
      return $str;
 
}

?>
