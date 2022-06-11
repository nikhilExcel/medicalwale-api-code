<?php
require_once("../config.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d H:i:s');
function send_gcm_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type, $word, $meaning)
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
            "tag" => $tag,
            'sound' => 'default',
            "notification_image" => $img_url,
            "notification_type" => $type,
            "notification_date" => $date,
            "notification_word" => $word,
            "notification_meaning" => $meaning
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

$dictionary_sql  = "SELECT word,meaning FROM HealthDictionary_new WHERE date=CURRENT_DATE() limit 1";
$dictionary_res  = mysqli_query($hconnection, $dictionary_sql);
$dictionary_list = mysqli_fetch_array($dictionary_res);
$word            = $dictionary_list['word'];
$meaning         = $dictionary_list['meaning'];

$sql        = "SELECT id as user_id,agent,token FROM users WHERE vendor_id='0' and token != ''";
$res        = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if ($count_data > 0) {
    while ($list = mysqli_fetch_array($res)) {
        $user_id = $list['user_id'];
        $agent   = $list['agent'];
		
		if($agent=='android'){
			//$reg_id_android[]   = trim($list['token']); 
            $reg_id_android[] = 'e9m86cUN18M:APA91bF6pYpY0f1mUtkECRY-DPakOPZ8Z-yAiYDPJC4bqYpDzmiSZ4wq-s0gUKWi2sHX4CWu9UjuFJpMknxfhF4hdUCW6VN4yb5gW-yeVWL5IoY4ca2H2zXvoWm8OtE4JLVkER_E9GvH';		
		}
		if($agent=='ios'){
			//$reg_id_ios[]   = trim($list['token']); 
            $reg_id_ios[] = 'e9m86cUN18M:APA91bF6pYpY0f1mUtkECRY-DPakOPZ8Z-yAiYDPJC4bqYpDzmiSZ4wq-s0gUKWi2sHX4CWu9UjuFJpMknxfhF4hdUCW6VN4yb5gW-yeVWL5IoY4ca2H2zXvoWm8OtE4JLVkER_E9GvH';	 
		}
		
        $title   = 'Word of the Day';
        $msg     = $word . ' ' . $meaning . '';        
        $date1   = date('Y-m-d');
        $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/medical_logo.png';
        
        $tag = 'text';        
        $type = 'HealthDictionary';        
        $s1 = "INSERT INTO All_notification_Mobile (user_id, package_name, booking_id, title, msg, notification_type, img_url,tag,order_date,notification_date) VALUES ('$user_id', '', '', '$title', '$msg', '$type','$img_url','$tag','$date1','$date1')";
        //$r1 = mysqli_query($hconnection, $s1);    
    }
	if($word != '') {
		//$reg_id_android = array_unique($reg_id_android);
		$chunk_android  = array_chunk($reg_id_android, 950);
		foreach ($chunk_android as $i => $data_android) {
			$reg_array_android = $data_android;
			$agent     = 'android';
			send_gcm_notify($title, $msg, $reg_array_android, $img_url, $tag, $agent, $type, $word, $meaning);  
		}
		
		//$reg_id_ios = array_unique($reg_id_ios);
		$chunk_ios  = array_chunk($reg_id_ios, 950);
		foreach ($chunk_ios as $j => $data_ios) {
			$reg_array_ios = $data_ios;
			$agent     = 'ios';
			send_gcm_notify($title, $msg, $reg_array_ios, $img_url, $tag, $agent, $type, $word, $meaning);  
		}
		
	}
}
?>
