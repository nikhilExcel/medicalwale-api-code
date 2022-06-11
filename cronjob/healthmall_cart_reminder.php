<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

$type    = 'Healthmall_cart';
require_once("../config.php");
require_once("function.php");
$sql="SELECT customer_id,added_date FROM `user_cart` WHERE added_date!='0000-00-00 00:00:00' AND is_notify='0' GROUP BY customer_id order by id ASC";
$res        = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if ($count_data > 0) {
    while ($list = mysqli_fetch_array($res)) {	
	    date_default_timezone_set('Asia/Calcutta');  
        $curr_time = date('Y-m-d H:i:s');			 
        $sys_time = date("Y-m-d H:i:s", strtotime($list['added_date'])); //2019-07-18 19:50:00        
              
        $sys_time_15_min  = date("Y-m-d", strtotime("+15 minutes", strtotime($sys_time)));
		$customer_id=$list['customer_id'];
        
	$query="SELECT id,name,token,agent FROM `users` WHERE id='$customer_id' order by id desc";	
	$get_query = mysqli_query($hconnection,$query);
	$info=mysqli_fetch_array($get_query);	
	$reg_id = $info['token'];
    $agent  = $info['agent'];
    $name  = ucfirst($info['name']);	
		
        if (strtotime($curr_time) > strtotime($sys_time_15_min)) {			
            $img_url = 'https://medicalwale.com/img/noti_icon.png';
            $tag     = 'text';               
            $title   = $name.', Products are waiting for you in cart';
            $msg     = 'Please complete your order!';     
            text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type);
        	$update= mysqli_query($hconnection,"UPDATE user_cart SET is_notify = '1' WHERE customer_id = '$customer_id'");
        }
    }
}
?>