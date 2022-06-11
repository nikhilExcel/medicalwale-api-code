<?php
require_once("config.php");
if(isset($_POST['user_id']))
{
$user_id=$_POST['user_id'];
$token_sql2 = "SELECT token FROM `mcustomer` WHERE id='$user_id' and token_status='1'";
$token_res2 = mysqli_query($connection,$token_sql2); 
$token_list2 = mysqli_fetch_array($token_res2);

$key=$token_list2["token"];
 
        if (!defined('API_ACCESS_KEY')) define( 'API_ACCESS_KEY', 'AIzaSyCQUTTdNIdbI9RGypZlXRgygucI2pLwFSE' );
        $tokenarray = array($key);
     
       
        $fields = array
        (
            'registration_ids'     => $tokenarray,
            'notification' => array (
                "body" => "Thanks for uploading your prescription with Medicalwale",
                "title" => "Prescription Order"
        )
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        return $result;
}

?>