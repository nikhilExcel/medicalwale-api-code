<?php
if (isset($_POST['post_id'])) {
    $result = array();
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $created_at   = date('Y-m-d H:i:s');
    $post_id      = addslashes($_POST['post_id']);
    $post_user_id = addslashes($_POST['post_user_id']);
    
    function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields  = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "notification_image" => $img_url,
                "tag" => $tag,
                'sound' => 'default',
                "notification_type" => 'healthwall_notifications',
                "notification_date" => $date,
                "post_id" => $post_id
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key='
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
    
    if ($post_id != '') {
        for ($i = 3680; $i <= 3795; $i++) {
            $sql   = "SELECT id FROM `article_likes` WHERE user_id='$i' and article_id='$post_id'";
            $res   = mysqli_query($hconnection, $sql);
            $count = mysqli_num_rows($res);
            if ($count > 0) {
            } else {
                $media  = mysqli_query($hconnection, "INSERT INTO `article_likes`(`article_id`, `user_id`) VALUES ('$post_id', '$i'");                
                $res2           = mysqli_query($hconnection, "SELECT token,agent,token_status FROM users WHERE id='$post_user_id' AND id<>'$i'");
                $customer_token_count = mysqli_num_rows($res2);
                if ($customer_token_count > 0) {
                    $token_list = mysqli_fetch_array($res2);
                    $res3       = mysqli_query($hconnection, "SELECT name FROM users WHERE id='$i'");
                    $getusr     = mysqli_fetch_array($res3);
                    
                    $res4      = mysqli_query($hconnection, "SELECT media.source FROM media LEFT JOIN users on users.avatar_id=media.id where users.id='$i'");
                    $img_count = mysqli_num_rows($res4);
                    if ($img_count > 0) {
                        $profile_query = mysqli_fetch_array($res4);
                        $img_file      = $profile_query['source'];
                        $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    } else {
                        $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }
                    $usr_name  = ucfirst($getusr['name']);
                    $agent     = $token_list['agent'];
                    $reg_id    = $token_list['token'];
                    $img_url   = $userimage;
                    $tag       = 'text';
                    $key_count = '1';
                    $title     = $usr_name . ' Beats on your Post';
                    $msg       = $usr_name . ' Beats on your post click here to view post.';
                    send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
                }
            }
        }
        if($media)
		{
			echo json_encode(array('status' => 200,'message' => 'success'));
		}
		else
		{
		    echo json_encode(array('status' => 204,'message' => 'fail1'));
		}
        
    } else {
        echo json_encode(array(
            'status' => 204,
            'message' => 'fail2'
        ));
    }
} else {
    echo json_encode(array(
        'status' => 204,
        'message' => 'fail3'
    ));
}
mysqli_close($connection);
?>