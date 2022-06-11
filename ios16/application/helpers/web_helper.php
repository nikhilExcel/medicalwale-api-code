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

?>