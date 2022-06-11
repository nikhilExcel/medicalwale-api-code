<?php
define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
function text_notification($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$user_id,$profile_id)
{
    date_default_timezone_set('Asia/Kolkata');
    $date = date('j M Y h:i A');

        $fields  = array(
        'to' => $reg_id,
        'priority' => "high",
        $agent === 'android' ? 'data' : 'notification' => array(
            "title" => $title,
            "message" => $msg,
            "tag" => $tag,
            'sound' => 'default',
            "notification_image" => $img_url,
            "icon" => $img_url,
            "notification_type" => $type,
            "notification_date" => $date,
            "user_id" => $user_id,
            "profile_id" => $profile_id
        )
    );
	if($agent=='android'){
	  $authorization_key='AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4';
	}
	if($agent=='ios'){
	  $authorization_key='AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE';
	}
	if($agent=='web'){
	  $authorization_key='AIzaSyAVqYMrFlhcUt-smdYqOI5ZasfbyL5G8RY';
	}
    $headers = array(
        GOOGLE_GCM_URL,
        'Content-Type: application/json',
        'Authorization: key='.$authorization_key
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
    echo $result;
    curl_close($ch);
}


function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "notifivation_image" => $img_url, "tag" => $tag, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '2') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
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
            echo $result;
        }

?>
