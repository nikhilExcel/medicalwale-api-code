<?php
require_once("config.php");
require_once("function.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d');
$notification_date=date('Y-m-d H:i:s');
$type         = 'Cycle_OnTheDay1';
$sql        = "SELECT `id`,`yes_no`,`user_id`,`name`,`profile_id`,`relationship`,`cycle_length`,`total_periode_day`,
DATE_FORMAT(STR_TO_DATE(`start_periode_data`, '%d-%m-%Y'), '%Y-%m-%d') AS start_period, DATE_ADD((DATE_FORMAT(STR_TO_DATE(`start_periode_data`, '%d-%m-%Y'), '%Y-%m-%d')) , INTERVAL `cycle_length` DAY) AS start_period_cycle, DATE_SUB((DATE_FORMAT(STR_TO_DATE(`start_periode_data`, '%d-%m-%Y'), '%Y-%m-%d')) , INTERVAL 0 DAY) AS `start_period_minus_0` FROM `menstural_cycle_data` where tracking_reson='Track cycle'";
$res        = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if ($count_data > 0) { 
    while ($list = mysqli_fetch_array($res)) {
        $user_id      = $list['user_id'];
	    $profile_id   = $list['profile_id'];
	    
	    $query_noti        = mysqli_query($connection, "SELECT id FROM `menstural_cycle_profile` WHERE is_notification='1' and user_id='$user_id' and id='$profile_id'");
        $is_notification = mysqli_num_rows($query_noti);
	    if($is_notification>0){
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
            //$current_date= '2019-04-30';
            
            $start_period_1_days  = date("Y-m-d", strtotime("+1 days", strtotime($start_period)));
            $start_period_2_days  = date("Y-m-d", strtotime("+2 days", strtotime($start_period)));
            $start_period_3_days  = date("Y-m-d", strtotime("+3 days", strtotime($start_period)));
            $start_period_4_days  = date("Y-m-d", strtotime("+4 days", strtotime($start_period)));
            $start_period_5_days  = date("Y-m-d", strtotime("+5 days", strtotime($start_period)));
            $start_period_6_days  = date("Y-m-d", strtotime("+6 days", strtotime($start_period)));
            $start_period_7_days  = date("Y-m-d", strtotime("+7 days", strtotime($start_period)));
            $start_period_8_days  = date("Y-m-d", strtotime("+8 days", strtotime($start_period)));
            $start_period_9_days  = date("Y-m-d", strtotime("+9 days", strtotime($start_period)));
            $start_period_10_days = date("Y-m-d", strtotime("+10 days", strtotime($start_period)));
            $start_period_11_days = date("Y-m-d", strtotime("+11 days", strtotime($start_period)));
            
            date_default_timezone_set('Asia/Kolkata');
            $current_time = date('H');
            
            if (strtotime($start_period) == strtotime($current_date)) {
                if ($vendor_id == '0') {
                    $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstrual_icon/same_days.png';
                    $tag     = 'text';
                    $msg     = '';
                    if($current_time>=7 && $current_time<=7.59){
                        $title   = $name . ', Keep a sanitary pad handy'; // morning 7-8
                            mysqli_query($hconnection, "INSERT INTO `myactivity_notification`(`user_id`,`notification_type`, `title`, `msg`,`notification_date`) VALUES ('$user_id','$type', '$title', '$msg','$notification_date')");
                        text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$user_id,$profile_id);
                    }
                    if($current_time>=12 && $current_time<=12.59){
                        $title   = $name . ', Have you got your period?'; //afternoon 12 - 13
                            mysqli_query($hconnection, "INSERT INTO `myactivity_notification`(`user_id`,`notification_type`, `title`, `msg`,`notification_date`) VALUES ('$user_id','$type', '$title', '$msg','$notification_date')");
                        text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$user_id,$profile_id);
                    }
                }
            }
            if($current_time>=8 && $current_time<=8.59){
                if ($yes_no == '0') {
                    
                    if (strtotime($current_date) == strtotime($start_period_2_days)) {
                        if ($vendor_id == '0') {
                            $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstrual_icon/2_plus_days.png';
                            $tag     = 'text';
                            $title   = $name . ', is it here yet?';
                            $msg     = '';
                            mysqli_query($hconnection, "INSERT INTO `myactivity_notification`(`user_id`,`notification_type`, `title`, `msg`,`notification_date`) VALUES ('$user_id','$type', '$title', '$msg','$notification_date')");
                            text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$user_id,$profile_id);
                        }
                    }
                    
                    if (strtotime($current_date) == strtotime($start_period_7_days)) {
                        if ($vendor_id == '0') {
                            $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstrual_icon/7_plus_days.png';
                            $tag     = 'text';
                            $title   = $name . ', is it still running late?';
                            $msg     = '';
                            mysqli_query($hconnection, "INSERT INTO `myactivity_notification`(`user_id`,`notification_type`, `title`, `msg`,`notification_date`) VALUES ('$user_id','$type', '$title', '$msg','$notification_date')");
                            text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$user_id,$profile_id);
                        }
                    }
                    
                    if (strtotime($current_date) == strtotime($start_period_11_days)) {
                        if ($vendor_id == '0') {
                            $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstrual_icon/11_plus_days.png';
                            $tag     = 'text';
                            $title   = $name . ', its time for a final call.';
                            $msg     = '';
                           mysqli_query($hconnection, "INSERT INTO `myactivity_notification`(`user_id`,`notification_type`, `title`, `msg`,`notification_date`) VALUES ('$user_id','$type', '$title', '$msg','$notification_date')");
                            text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$user_id,$profile_id);
                        }
                        $id                 = $list['id'];
                        $start_period_cycle = date("d-m-Y", strtotime($list['start_period_cycle']));
                        $update             = mysqli_query($hconnection, "UPDATE `menstural_cycle_data` SET `start_periode_data`='$start_period_cycle' WHERE id='$id'");
                    }
                }
            }
	    }
    }
}
?>
