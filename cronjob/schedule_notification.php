<?php
require_once("../config.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d H:i:s');
$reg_id_ios='';
$reg_id_android='';
function send_gcm_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type, $listing_id, $tag_url, $list_type, $healthmall_id, $med_video_id, $article_id)
{
    date_default_timezone_set('Asia/Kolkata');
    $date = date('j M Y h:i A');
    if (!defined("GOOGLE_GCM_URL"))
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
    $fields  = array(
        'registration_ids' => $reg_id,
        'priority' => "high",
        $agent === 'android' ? 'data' : 'notification' => array(
            "title" => $title,
            "message" => $msg,
            "notification_image" => $img_url,
            "tag" => $tag,
            'sound' => 'default',
            'listing_id' => $listing_id,
            "notification_type" => $type,
            "notification_date" => $date,
            "image" => $tag_url,
            "list_type" => $list_type,
            "healthmall_id" => $healthmall_id,
            "med_video_id" => $med_video_id,
            "article_id" => $article_id
        )
    );
    $headers = array(
        GOOGLE_GCM_URL,
        'Content-Type: application/json',
        $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
    );
    $ch      = curl_init();
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
}

$sys_time       = date('H:i');
$dictionary_sql = "SELECT * FROM schedule_notification WHERE schedule_status=0 AND schedule_date = CURRENT_DATE() AND schedule_time = '$sys_time'";
$dictionary_res = mysqli_query($hconnection, $dictionary_sql);
$count_data     = mysqli_num_rows($dictionary_res);

if ($count_data > 0) {
    while ($row_main = mysqli_fetch_array($dictionary_res)) {
        $id                = $row_main['id'];
        $agent_type        = $row_main['agent_type'];
        $listing_id        = $row_main['listing_id'];
        $post_id           = $row_main['post_id'];
        $order_id          = $row_main['order_id'];
        $order_date        = date('Y-m-d', strtotime($row_main['order_date']));
        $order_status      = $row_main['order_status'];
        $booking_id        = $row_main['booking_id'];
        $invoice_no        = $row_main['invoice_no'];
        $notification_type = $row_main['notification_type'];
        $notification_date = date('Y-m-d', strtotime($row_main['notification_date']));
        $package_id        = $row_main['package_id'];
        $package_name      = $row_main['package_name'];
        $title             = $row_main['title'];
        $list_type         = $row_main['list_type'];
        $msg               = $row_main['msg'];
        $img_url           = $row_main['img_url'];
        $tag               = $row_main['tag'];
        $healthmall_id     = $row_main['healthmall_id'];
        $pdf_link          = $row_main['pdf_link'];
        $booking_from      = $row_main['booking_from'];
        $booking_to        = $row_main['booking_to'];
        $presription_id    = $row_main['presription_id'];
        $article_id        = $row_main['article_id'];
        $med_video_id      = $row_main['med_video_id'];
        $tag_url           = $row_main['tag_image_url'];
        
        $s1 = "INSERT INTO other_notifications (user_id, listing_id, post_id, order_id, order_date, order_status, booking_id,invoice_no,notification_type,notification_date,package_id,package_name,title,list_type,msg,img_url,tag,healthmall_id,pdf_link,booking_from,booking_to,presription_id,article_id,med_video_id,noti_type)
       VALUES ('0', '$listing_id', '$post_id', '$order_id', '$order_date', '$order_status','$booking_id','$invoice_no','$notification_type','$notification_date','$package_id','$package_name','$title','$list_type','$msg','$img_url','$tag','$healthmall_id','$pdf_link','$booking_from','$booking_to','$presription_id','$article_id','$med_video_id','$agent_type')";
	   $r1 = mysqli_query($hconnection, $s1);
        
        $s = "UPDATE schedule_notification set schedule_status=1 WHERE id='$id'";
        $r = mysqli_query($hconnection, $s);
        
        if ($agent_type != "both") {
            $userql = "SELECT id as user_id,token,agent FROM users WHERE vendor_id='0' AND agent='$agent_type' AND token != '' AND LENGTH(token) > 20  order by id DESC";
        } else {
            $userql = "SELECT id as user_id,token,agent FROM users WHERE vendor_id='0' AND token != '' AND LENGTH(token) > 20 AND agent!='' order by id DESC";
        }
        $userres = mysqli_query($hconnection, $userql);
        while ($userlist = mysqli_fetch_array($userres)) {
            $agent   = $userlist['agent'];
            $user_id = $userlist['user_id'];
            if ($agent == 'android') {
                $reg_id_android[] = trim($userlist['token']);	
            }
            if ($agent == 'ios') {
                $reg_id_ios[] = trim($userlist['token']);
            }
        }
        if ($count_data > 0) {
	    if(!empty($reg_id_android)){
            $reg_id_android = array_unique($reg_id_android);
            $chunk_android  = array_chunk($reg_id_android, 950);
            foreach ($chunk_android as $i => $data_android) {
                $reg_array_android = $data_android;
                $agent             = 'android';
              send_gcm_notify($title, $msg, $reg_array_android, $img_url, $tag, $agent, $notification_type, $listing_id, $tag_url, $list_type, $healthmall_id, $med_video_id, $article_id);
		 
	    }
	    }
	    if(!empty($reg_id_ios)){
            $reg_id_ios = array_unique($reg_id_ios);
            $chunk_ios  = array_chunk($reg_id_ios, 950);
            foreach ($chunk_ios as $j => $data_ios) {
                $reg_array_ios = $data_ios;
                $agent         = 'ios';
               send_gcm_notify($title, $msg, $reg_array_ios, $img_url, $tag, $agent, $notification_type, $listing_id, $tag_url, $list_type, $healthmall_id, $med_video_id, $article_id);
          
	    }
	    }
        }
    }
}
?>
