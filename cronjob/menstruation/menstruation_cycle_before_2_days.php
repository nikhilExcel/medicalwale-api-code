<?php
require_once("config.php");
require_once("function.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d');
$type    = 'Cycle_Before2Days'; 
$sql = "SELECT `id`,`user_id`,`name`,`profile_id`,`relationship`,`cycle_length`,`total_periode_day`,
DATE_FORMAT(STR_TO_DATE(`start_periode_data`, '%d-%m-%Y'), '%Y-%m-%d') AS start_period, DATE_ADD((DATE_FORMAT(STR_TO_DATE(`start_periode_data`, '%d-%m-%Y'), '%Y-%m-%d')) , INTERVAL `cycle_length` DAY) AS start_period_cycle, DATE_SUB((DATE_FORMAT(STR_TO_DATE(`start_periode_data`, '%d-%m-%Y'), '%Y-%m-%d')) , INTERVAL 2 DAY) AS `start_period_minus_2` FROM `menstural_cycle_data` HAVING CURDATE() BETWEEN start_period AND start_period_cycle";
$res        = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if ($count_data > 0) {
    while ($list = mysqli_fetch_array($res)) {
        $user_id  = $list['user_id'];
    	$profile_id  = $list['profile_id'];
    	
    	$query_noti        = mysqli_query($connection, "SELECT id FROM `menstural_cycle_profile` WHERE is_notification='1' and user_id='$user_id' and id='$profile_id'");
        $is_notification = mysqli_num_rows($query_noti);
    	if($is_notification>0){
            $start_period  = date("Y-m-d", strtotime($list['start_period_cycle']));
        	$query="SELECT id,token,agent,vendor_id FROM `users` WHERE id='$user_id' order by id desc";	
        	$get_query = mysqli_query($connection,$query);
        	$info=mysqli_fetch_array($get_query);	
        	$reg_id = $info['token'];
            $agent  = $info['agent'];
            $name  = $list['name'];		
            $vendor_id = $info['vendor_id'];
            
            $date_minus_2_days   = date("Y-m-d", strtotime("+2 days",  strtotime($current_date)));
            if (strtotime($start_period) == strtotime($date_minus_2_days)) {
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstrual_icon/2_days.png';
                    $tag     = 'text';               
                    $title   = $name.", it's time to stock up the essentials, 2 days to go";
                    $msg     = '';   
                    $insert_title   = addslashes($name.", it's time to stock up the essentials, 2 days to go");
		            $insert_msg     = '';		
		            $notification_date=date('Y-m-d H:i:s');
		            $insert=mysqli_query($hconnection, "INSERT INTO `myactivity_notification`(`title`,`msg`,`img_url`,`tag`,`order_status`,`order_date`,`order_id`,`post_id`,`listing_id`,`booking_id`,`invoice_no`,`user_id`,`notification_type`,`notification_date`) VALUES ('$insert_title','$insert_msg','','','','','','','','','','$user_id','Cycle_Before2Days','$notification_date')");
                    text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$user_id,$profile_id);
        	}
    	}
    }
}
?>
