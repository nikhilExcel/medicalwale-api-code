<?php 

function crul($ur,$data)
{
       $u="http://live.medicalwale.com/ios21/";
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
function crul_sms($phone, $message)
{
      	// Account details
 $username = 'medicalwale';
 $password = 'f62b2613';
// Message details
 $senderid = 'MDWALE';
 $type = '1';
 $product = '1';
 $number = $phone;


 $message = urlencode($message);
// API credentials
 $credentials = 'username='. $username . '&password='. $password;
// prepare data for GET request
$data = '&sender='. $senderid . '&mobile='. $number .'&type='. $type .'&product='. $product . '&message='. $message;
// make url to post using cURL

 $url = 'http://makemysms.in/api/sendsms.php?'. $credentials . $data;
 
// Configure cURL options
 $options = array (CURLOPT_RETURNTRANSFER => true , // return web page
 CURLOPT_HEADER => false , // don't return headers
 CURLOPT_FOLLOWLOCATION => false , // follow redirects
 CURLOPT_ENCODING => "" , // handle compressed
 CURLOPT_USERAGENT => "test" , // who am i
 CURLOPT_AUTOREFERER => true , // set referer on redirect
 CURLOPT_CONNECTTIMEOUT => 120 , // timeout on connect
 CURLOPT_TIMEOUT => 120 , // timeout on response
 CURLOPT_MAXREDIRS => 10 ); // stop after 10 redirects
 
// Send the GET request with cURL
 $ch = curl_init ( $url ); 
 curl_setopt_array ( $ch, $options ); 
 $content = curl_exec ( $ch ); 
 $err = curl_errno ( $ch ); 
 $errmsg = curl_error ( $ch ); 
 $header = curl_getinfo ( $ch ); 
 $httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE ); 
 curl_close ( $ch ); 
       return $header;
}


/*function healthwall_adver_crul($ur,$data)
{
      
       $url=$ur;
       $ch = curl_init($url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
     //  curl_setopt($ch, CURLOPT_HTTPHEADER,[,'Authorizations:25iwFyq/LSO1U','Client-Service:frontend-client','Auth-Key:medicalwalerestapi']);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       $result = curl_exec($ch);
       curl_close($ch);
       print_r($result);
       return $result;
}*/

/*function healthwall_adver_crul($ur,$data)
{
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ur);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_PORT, 8011);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            
            $httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE); // this results 0 every time
            $response = curl_exec($ch);
          
            if ($response === false) 
                $response = curl_error($ch);
            
            echo stripslashes($response);
              print_r($response);
            curl_close($ch);
  
 
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
