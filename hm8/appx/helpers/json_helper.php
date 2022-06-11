<?php 

    function crul_rapid_delivery($u,$data)
    {
        // print_r($data);
        $u=$u;
        $url="trace.rapiddelivery.co/api/".$u;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }  

?>