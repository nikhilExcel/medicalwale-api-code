<?php 
require_once ("config.php"); 
if(isset($_POST['order_id']) && isset($_POST['type']))
{
$date=date('Y-m-d');
$updated_at = date('Y-m-d H:i:s');
$order_id=$_POST['order_id'];
$type=$_POST['type'];
$order_status=$_POST['type'];
$cancel_reason=$_POST['cancel_reason'];

if($type=='Order Confirmed')
{
$update= mysqli_query($connection,"UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Confirmed',`cancel_reason`='',`action_by`='customer' WHERE order_id='$order_id'");
$updated_at=date('j M Y h:i A', strtotime($updated_at));
function send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name) {	           	
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to'                  => $reg_id ,
            'priority'             => "high",
            'data'                => array("title" => $title, "message" => $msg, "image"=> $img_url, "tag" => $tag,"notification_type" => "order","order_status"=>$order_status,"order_date"=>$order_date,"order_id"=>$order_id,"invoice_no"=>$invoice_no,"name"=>$name,"listing_name"=>$listing_name)
        );
		$headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        //echo $result;
    }

      $res_order   = mysqli_query($connection,"select user_id,listing_id,invoice_no,name,listing_name from user_order where order_id='$order_id' limit 1");
      $order_info = mysqli_fetch_array($res_order);
	  $listing_id=$order_info['listing_id'];
	  $invoice_no=$order_info['invoice_no'];
	  $name=$order_info['name'];
	  $listing_name=$order_info['listing_name'];
	  $updated_at=date('j M Y h:i A', strtotime($updated_at));
	  $res_token   = mysqli_query($connection,"select token,token_status from users where id='$listing_id' limit 1");
      $token_value = mysqli_fetch_array($res_token);
	  $token_status=$token_value['token_status'];
	  if($token_status>0)
	  {
	    $reg_id = $token_value['token'];		
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
		$tag = 'text';
		$title='Order Confirmed';
		$msg = 'Kindly deliver the order';
		send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name);
	  }
echo json_encode(array("status" => 201,"message" => "Order Confirmed"));
mysqli_close($connection);
}
if($type=='Order Cancelled')
{
$res_status  = mysqli_query($connection,"select order_status from user_order where order_id='$order_id' limit 1");
$o_status = mysqli_fetch_array($res_status);
$check_status=$o_status['order_status'];
if($check_status=='Order Delivered')
{
echo json_encode(array("status" => 201,"message" => "Order Delivered"));
mysqli_close($connection);
}
else
{
$update= mysqli_query($connection,"UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='customer' WHERE order_id='$order_id'");

if($update)
{
echo json_encode(array("status" => 201,"message" => "Order Cancelled"));

function send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name) {	           	
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to'                  => $reg_id ,
            'priority'             => "high",
            'data'                => array("title" => $title, "message" => $msg, "image"=> $img_url, "tag" => $tag,"notification_type" => "order","order_status"=>$order_status,"order_date"=>$order_date,"order_id"=>$order_id,"invoice_no"=>$invoice_no,"name"=>$name,"listing_name"=>$listing_name)
        );
		$headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        //echo $result;
    }

      $res_order   = mysqli_query($connection,"select listing_id,invoice_no,name,listing_name from user_order where order_id='$order_id' limit 1");
      $order_info = mysqli_fetch_array($res_order);
	  $listing_id=$order_info['listing_id'];
	  $invoice_no=$order_info['invoice_no'];
	  $name=$order_info['name'];
	  $listing_name=$order_info['listing_name'];
      $updated_at=date('j M Y h:i A', strtotime($updated_at));
	  $res_token   = mysqli_query($connection,"select token,token_status from users where id='$listing_id' limit 1");
      $token_value = mysqli_fetch_array($res_token);
	  $token_status=$token_value['token_status'];
	  if($token_status>0)
	  {
	    $reg_id = $token_value['token'];		
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
		$tag = 'text';
		$title='Order Cancelled';
		$msg = 'You order has been cancelled';
		send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name);
	  }
mysqli_close($connection);
}
else
{
echo json_encode(array("status" => 201,"message" => "failed"));
mysqli_close($connection);
}
}
}
}
else
{
echo json_encode(array("status" => 201,"message" => "required"));
mysqli_close($connection);
} 
?>