<?php
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $current_date = date('Y-m-d H:i:s');
   function send_gcm_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "tag" => $tag,
                'sound' => 'default',
                "notification_image" => $img_url,
                "notification_type" => $type,
                "notification_date" => $date
               
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
       // print_r($result);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }
    
     $dictionary_sql = "SELECT * FROM diet_user_package_history WHERE booking_to = CURRENT_DATE() AND complete_status=0 ";
    $dictionary_res = mysqli_query($hconnection, $dictionary_sql);
    $count_data = mysqli_num_rows($dictionary_res);
   
    if($count_data>0)
    {
        
      
        while ($list = mysqli_fetch_array($dictionary_res)) {
            $user_id = $list['user_id']; 
            
            $s = "UPDATE diet_user_package_history set complete_status=1 WHERE user_id='$user_id' AND booking_to = CURRENT_DATE() AND complete_status=0 ";
            $r = mysqli_query($hconnection, $s);
   
            $userql = "SELECT token,agent FROM users where id='$user_id' limit 1";
            $userres = mysqli_query($hconnection, $userql);
            $userlist=mysqli_fetch_array($userres);
                 
             
               
            //$msg ='Dear User, Your Missbelly Dietplan Package is over today. Please renew your package. If renewed please ignore.';
            $msg ='Hey, your Miss Belly package is going to end today, would you like to renew it?';
            $reg_id =$userlist['token'];
            $agent =$userlist['agent'];
            if($reg_id == '')
            {
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/missbelly.png';
                $tag = 'text';
                $title = 'Missbelly Package Over';
                $type= 'Missbelly_Package_over';
                $packageid = $list['id'];
                $booking_id = $list['booking_id'];
          
                 $s = "INSERT INTO diet_plan_notifications (user_id, package_id, booking_id, title, msg, notification_type, created_at)
       VALUES ('$user_id', '$packageid', '$packageid', '$title', '$msg', '$type', '$current_date')";
                $r = mysqli_query($hconnection, $s);
            }
            else
            {
            $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/missbelly.png';
            $tag = 'text';
            $title = 'Miss Belly Package Over';
            $type= 'Missbelly_Package_over';
            $packageid = $list['id'];
            $booking_id = $list['booking_id'];
            $date1 = date('Y-m-d');
          
             $s = "INSERT INTO diet_plan_notifications (user_id, package_id, booking_id, title, msg, notification_type, created_at)
   VALUES ('$user_id', '$packageid', '$packageid', '$title', '$msg', '$type', '$current_date')";
    $r = mysqli_query($hconnection, $s);
      
        $s1 = "INSERT INTO All_notification_Mobile (user_id, package_name, booking_id, title, msg, notification_type, img_url,tag,order_date,notification_date)
   VALUES ('$user_id', '$packageid', '$booking_id', '$title', '$msg', '$type','$img_url','$tag','$date1','$date1')";
    $r1 = mysqli_query($hconnection, $s1);
    
                $userql1 = "SELECT * FROM `notification_switch_control` where user_id='$user_id' limit 1";
                $userres1 = mysqli_query($hconnection, $userql1);
                $userlist1=mysqli_fetch_array($userres1);
                if(!empty($userlist1))
                {
                    $count=mysqli_num_rows($userlist1);
                    if($count > 0)
                    {
                        $status =$userlist1['status'];
                        if($status=="on")
                        {
                              send_gcm_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type); 
                        }
                    }
                     else
                    {
                          send_gcm_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type); 
                    }
                }
                 else
                    {
                          send_gcm_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type); 
                    }
        }
      
    }
    
    }
   
   
?>

