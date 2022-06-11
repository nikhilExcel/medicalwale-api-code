<?php
require_once("config.php");
require_once("function.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d');
$notification_date=date('Y-m-d H:i:s');
$type         = 'Cycle_OnTheDay1';
$sql        = "SELECT * FROM `menstural_cycle_data` where DATE_FORMAT(STR_TO_DATE(`start_periode_data`, '%d-%m-%Y'), '%Y-%m-%d') BETWEEN DATE_SUB(CURDATE() ,INTERVAL 20 MONTH) AND DATE_SUB(CURDATE() ,INTERVAL 1 MONTH ) GROUP by profile_id ORDER BY `id` desc";
$res        = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if ($count_data > 0) { 
    while ($list = mysqli_fetch_array($res)) {
        $user_id      = $list['user_id'];
	      $profile_id   = $list['profile_id'];
	      $start_periode_date   = $list['start_periode_data'];
	    
	      $query_noti        = mysqli_query($connection, "SELECT id FROM `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
        $is_notification = mysqli_num_rows($query_noti);

        $query2    = mysqli_query($connection, "SELECT id,DATE_FORMAT(STR_TO_DATE(`start_periode_data`, '%d-%m-%Y'), '%Y-%m-%d') AS start_period FROM `menstural_cycle_data` WHERE user_id='$user_id' and profile_id='$profile_id' HAVING start_period>'$start_periode_date' order by start_period desc limit 1");
        $count_new = mysqli_num_rows($query2);
	      if($is_notification>0 && $count_new==0){
            $yes_no       = $list['yes_no'];
            $start_period = date("Y-m-d", strtotime($list['start_period_cycle']));
            
            $query     = "SELECT id,token,agent,vendor_id FROM `users` WHERE id='$user_id' order by id desc";
            $get_query = mysqli_query($connection, $query);
            $info      = mysqli_fetch_array($get_query);
            $reg_id    = $info['token'];
            $agent     = $info['agent'];
            $vendor_id = $info['vendor_id'];
            $name      = $list['name'];
            //$current_date   = date("Y-m-d",  strtotime($current_date));
            
            $current_date = date("Y-m-d", strtotime("+0 days", strtotime($current_date)));
           
            date_default_timezone_set('Asia/Kolkata');
            $current_time = date('H');
            
            $img_url = 'https://medicalwale.com/img/noti_icon.png';
            $tag     = 'text';
            $msg     = 'Kindly fill it timely to track your menstruation.';
            $title   = "Hi ".$name.", you haven't updated details since a while.";
            

                text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$user_id,$profile_id);
                $title = addslashes($title);
                mysqli_query($hconnection, "INSERT INTO `myactivity_notification`(`user_id`,`notification_type`, `title`, `msg`,`notification_date`) VALUES ('$user_id','$type', '$title', '$msg','$notification_date')");
            
	    }
    }
}
?>
