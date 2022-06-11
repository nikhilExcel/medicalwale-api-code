<?php
require_once("config.php");
$result1 = array();
$result2 = array(); 
if(isset($_POST['user_id']) && isset($_POST['type']) )
{
$user_id=$_POST['user_id'];
$type=$_POST['type'];
$token_sql2 = "SELECT id,token FROM `mcustomer` WHERE id='$user_id' and token_status='1'";
$token_res2 = mysqli_query($connection,$token_sql2); 
$token_count = mysqli_num_rows($token_res2);
if($token_count>0)
{
$token_list2 = mysqli_fetch_array($token_res2);
$key=$token_list2["token"];

$body='';
$title='';
if($type=='order')
{
$body="Thanks for placing your order with Medicalwale";
$title="General Medicines Order";
}
if($type=='prescription')
{
$body="Thanks for uploading your prescription with Medicalwale";
$title="Prescription Order";
}
$error_msg='Sent';
$true_false='true';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
 
        if (!defined('API_ACCESS_KEY')) define( 'API_ACCESS_KEY', 'AIzaSyCQUTTdNIdbI9RGypZlXRgygucI2pLwFSE' );
        $tokenarray = array($key);
     
       
        $fields = array
        (
            'registration_ids'     => $tokenarray,
            'notification' => array (
                "body" => $body,
                "title" => $title
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
else
{
$error_msg='Token off';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
}
else
{
$error_msg='Please send all fields';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
?>