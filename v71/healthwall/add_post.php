<?php
if (isset($_POST['user_id'])) {
    $result = array();
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');
    $uni = date('YmdHis');
    $tag = addslashes($_POST['tag']);
    $category = addslashes($_POST['category']);
    $post = trim(addslashes($_POST['post']));
    $type = addslashes($_POST['type']);
    $user_id = addslashes($_POST['user_id']);
    $is_anonymous = addslashes($_POST['is_anonymous']);
    $caption = $_POST['caption'];
    $post_location = $_POST['post_location'];
    $healthwall_category = addslashes($_POST['healthwall_category']);
    $article_title = $_POST['article_title'];
    $article_title = str_replace("'", "\'", $article_title);
    $article_image = addslashes($_POST['article_image']);
    $article_domain_name = addslashes($_POST['article_domain_name']);
    $article_url = addslashes($_POST['article_url']);
    if ($type == 'write_post') {
        $article_desc = '<a href="' . $article_url . '" target="_blank" style="text-decoration: none;"><div id="thumbnail"><img src="close.png" id="remove" width="10px"><img src="' . $article_image . '" style="width: 100%;object-fit: cover;height: 189px;"></div><div id="texts"><div id="title"><span style="font-weight:bold;text-align: justify;">' . $article_title . '</span></div> <div id="desc" style="text-align: justify;"><span style="font-size:12px"></span></div> <div id="meta"><div id="domain">' . $article_domain_name . '</div><div id="author"></div><div class="clear"></div></div></div></a>';
    } else {
        $article_desc = '';
    }
    if ($is_anonymous > 0) {
        $is_anonymous = addslashes($_POST['is_anonymous']);
    } else {
        $is_anonymous = '0';
    }
    function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }
    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
    $video_format = array("mp4", "avi", "flv", "wmv", "mov", "3gp", "MP4", "AVI", "FLV", "WMV", "MOV", "3GP");
    include('../s3_config.php');
    $image = count($_FILES['image']['name']);
    if ($user_id != '') {
        $sql = "SELECT id FROM `users` WHERE id='$user_id' limit 1";
        $res = mysqli_query($hconnection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            mysqli_set_charset($connection, 'utf8');
            $post_insert = mysqli_query($connection, "INSERT INTO `posts`(`healthwall_category`,`description`,`article_desc`,`type`,`tag`,`category`,`is_anonymous`,`user_id`, `article_title`, `article_image`, `article_domain_name`,`article_url`, `active`, `created_at`, `updated_at`,`post_location`) VALUES ('$healthwall_category','$post','$article_desc','$type', '$tag','$category','$is_anonymous', '$user_id', '$article_title','$article_image','$article_domain_name','$article_url' ,'1', '$date', '$date','$post_location')");
            if ($post_insert) {
                $post_id = mysqli_insert_id($connection);
                if ($type == 'question') {

                    //NOTIFICATIONS STARTS
                    $sql_ = "SELECT id FROM business_category WHERE CONCAT(',',doctors_type_id,',') REGEXP ',($healthwall_category),'";
                    $res_ = mysqli_query($hconnection, $sql_);
                    $count_ = mysqli_num_rows($res_);
                    $user_id_array = array();
        			if($count_>0)
        			{
        				while ($raw_ = mysqli_fetch_array($res_)) {
        					$id_ = $raw_['id'];
        					$sql__ = "SELECT user_id,category FROM doctor_list WHERE CONCAT(',',category,',') REGEXP ',($id_),'";
                            $res__ = mysqli_query($hconnection, $sql__);
                            $count__ = mysqli_num_rows($res__);
                			if($count__>0)
                			{
                				while ($raw__ = mysqli_fetch_array($res__)) {
                					$user_id_array_1 = array($raw__['user_id']);
                					$user_id_array = array_merge($user_id_array, $user_id_array_1);
                				}
                			}
        				}
        			}
                    $parent_id = array_unique($user_id_array);
                   // print_r($parent_id);
                    foreach($parent_id as $pi){
                        // WEB NOTIFICATION STARTS
                        $current_date = curr_date();
                		$res_noti = mysqli_query($connection, "INSERT INTO `notifications`(`post_id`, `timeline_id`,`type`, `user_id`, `notified_by`, `seen`, `description`, `created_at`, `updated_at`) VALUES ('$post_id','$user_id','comment','$user_id','$pi','1',' Aske a question','$current_date','$current_date')");
                        //$count_noti = mysqli_num_rows($res_noti);
                		// WEB NOTIFICATION ENDS

                		// DOCTOR APP NOTIFICATION STARTS
                        $sql__token = "SELECT name,phone,email,token,agent,token_status FROM users WHERE id='$pi'";
                        $res__token = mysqli_query($hconnection, $sql__token);
                        $count__token = mysqli_num_rows($res__token);
            			if($count__token>0)
                			{
                                //$token_status = $customer_token->row_array();
                                $token_status = mysqli_fetch_array($res_);
                                //    $getusr = $user_plike->row_array();
                                $usr_name     = $token_status['name'];
                                $user_email   = $token_status['email'];
                                $agent        = $token_status['agent'];
                                $reg_id       = $token_status['token'];
                                $description  = "";
                                $img_url      = 'https://medicalwale.com/img/medical_logo.png';
                                $tag          = 'text';
                                $key_count    = '1';
                                $title        = $usr_name . ' asked a question?';
                                $msg          = $usr_name . '  asked a question.\n' + $post;
                                //$this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $date, $from_time, $to_time, $appointment_id, $consultation_type);
                                /* notification to send in the doctor app for appointment confirmation*/

                                date_default_timezone_set('Asia/Kolkata');
                                $date = date('j M Y h:i A');
                                if (!defined("GOOGLE_GCM_URL")) {
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
                                            "notification_type" => 'ask_question',
                                        )
                                    );
                                    /*IOS registration is left */
                                    $headers = array(
                                        GOOGLE_GCM_URL,
                                        'Content-Type: application/json',
                                        $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
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
                            }
                        // DOCTOR APP NOTIFICATION ENDS
                    }
                    //NOTIFICATIONS ENDS

                    define("POST_URL", "https://live.medicalwale.com/v70/healthwall/post_comment");
                    $comment = 'Thank you for connecting with us, doctors will be answering your query within 48 hours. In case of emergency, we would suggest you to visit your nearest doctor. We hope to serve you better everyday. Meanwhile you can explore various beneficial features on our App.';
                    $fields = array(
                        'user_id' => '1515',
                        'post_id' => $post_id,
                        'comment' => $comment,
                        'post_user_id' => $user_id
                    );
                    $headers = array(
                        'Client-Service:frontend-client',
                        'Auth-Key:medicalwalerestapi',
                        'Content-Type:application/json',
                        'User-ID:1',
                        'Authorizations:25iwFyq/LSO1U'
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, POST_URL);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
                if ($image > 0) {
                    $flag = '1';
                    $video_flag = '1';
                    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                        $img_name = $key . $_FILES['image']['name'][$key];
                        $img_size = $_FILES['image']['size'][$key];
                        $img_tmp = $_FILES['image']['tmp_name'][$key];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                                if (in_array($ext, $img_format)) {
                                    $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_media/image/' . $actual_image_name;
                                    if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                        if ($flag > 0) {
                                            $img_url = 'https://medicalwale.s3.amazonaws.com/images/healthwall_media/image/' . $actual_image_name;
                                            $imagedetails = getimagesize($img_url);
                                            $widths = $imagedetails[0];
                                            $heights = $imagedetails[1];
                                            $flag = '0';
                                        }
                                        $media = mysqli_query($hconnection, "INSERT INTO `media`(`caption`,`title`, `type`, `source`, `created_at`, `updated_at`) VALUES ('$caption[$key]','$actual_image_name', 'image', '$actual_image_name', '$date','$date')");
                                        $media_id = mysqli_insert_id($hconnection);
                                        $post_media = mysqli_query($connection, "INSERT INTO `post_media`(`post_id`, `media_id`, `created_at`, `updated_at`, `img_width`, `img_height`) VALUES ('$post_id', '$media_id', '$date', '$date', '$widths', '$heights')");
                                    }
                                }
                                if (in_array($ext, $video_format)) {
                                    $uniqid = uniqid() . date("YmdHis");
                                    $actual_video_name = $uniqid . "." . $ext;
                                    $actual_video_path = 'images/healthwall_media/video/' . $actual_video_name;
                                    if ($s3->putObjectFile($img_tmp, $bucket, $actual_video_path, S3::ACL_PUBLIC_READ)) {
                                        $video_width = '300';
                                        $video_height = '160';
                                        $media = mysqli_query($hconnection, "INSERT INTO `media`(`caption`,`title`, `type`, `source`, `created_at`, `updated_at`) VALUES ('$caption[$key]','$actual_video_name', 'video', '$actual_video_name', '$date','$date')");
                                        $media_id = mysqli_insert_id($hconnection);
                                        $post_media = mysqli_query($hconnection, "INSERT INTO `post_media`(`post_id`, `media_id`, `created_at`, `updated_at`, `video_width`, `video_height`) VALUES ('$post_id', '$media_id', '$date', '$date', '$video_width', '$video_height')");
                                    }
                                }
                        }
                    }
                }
                echo json_encode(array(
                    'status' => 200,
                    'message' => 'success'
                ));
            } else {
                echo json_encode(array(
                    'status' => 201,
                    'message' => 'fail1'
                ));
            }
        } else {
            echo json_encode(array(
                'status' => 202,
                'message' => 'fail2'
            ));
        }
    } else {
        echo json_encode(array(
            'status' => 203,
            'message' => 'fail3'
        ));
    }

    //mysqli_close($connection);
} else {
    echo json_encode(array(
        'status' => 204,
        'message' => 'fail4'
    ));
}

// function insert_notification_post_question($user_id, $post_id, $name, $doctor_id){

// 	}
function curr_date(){

        date_default_timezone_set('Asia/Calcutta');
        return $date = date('Y-m-d H:i:s');
}
?>
