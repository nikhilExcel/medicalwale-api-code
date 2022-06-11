<?php

// Include confi.php
include_once('../../config.php');

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $user_query = mysqli_query($conn, "SELECT id FROM `users` where id='$user_id'");
    $user_count = mysqli_num_rows($user_query);
    if ($user_count > 0) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));

            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }

        $resultpost = array();

        $ask_saheli_postQuery = mysqli_query($conn, "SELECT sex_education_question.id,sex_education_question.age,sex_education_question.question,sex_education_question.date,sex_education_ask_expert.name AS user_name,sex_education_character.image AS c_image  FROM  `sex_education_question` INNER JOIN `sex_education_ask_expert` ON sex_education_question.user_id=sex_education_ask_expert.user_id INNER JOIN `sex_education_character` ON sex_education_ask_expert.image=sex_education_character.id order by sex_education_question.id desc");
        $ask_saheli_post_count = mysqli_num_rows($ask_saheli_postQuery);
        if ($ask_saheli_post_count > 0) {
            while ($row = mysqli_fetch_array($ask_saheli_postQuery)) {
                extract($row);
                $id = $id;
                $user_name = $user_name;
                $question = $question;
                $date = $date;
                $image = $c_image;
                $age = $age;

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                    //list($width, $height) = getimagesize($image);
                } else {
                    $image = 'no image';
                    //$width='';
                    //$height='';
                }

                $postQuery_Hide = mysqli_query($conn, "SELECT id FROM `sex_education_hide` where user_id='$user_id' AND post_id='$id'");
                $hide_row = mysqli_fetch_array($postQuery_Hide);
                $Hide_post_val = $hide_row['is_hide'];


                $count_query = mysqli_query($conn, "SELECT id FROM `sex_education_likes` where post_id='$id'");
                $like_count = mysqli_num_rows($count_query);


                $comment_query = mysqli_query($conn, "SELECT id FROM `ask_saheli_comment` where post_id='$id'");
                $post_count = mysqli_num_rows($comment_query);
                $post_count = '0';

                $like_count_query = mysqli_query($conn, "SELECT id FROM `sex_education_likes` where user_id='$user_id' and post_id='$id'");
                $like_yes_no = mysqli_num_rows($like_count_query);



                $date = get_time_difference_php($date);

                $resultpost[] = array('id' => $id, 'user_name' => $user_name, 'question' => $question, 'age' => $age, 'image' => $image, 'like_count' => $like_count, 'like_yes_no' => $like_yes_no, 'comment_count' => $post_count, 'post_hide' => $Hide_post_val, 'date' => $date,);
            }

            $json = array("status" => 1, "msg" => "success", "count" => sizeof($resultpost), "data" => $resultpost);
        } else {
            $json = array("status" => 0, "msg" => "post not found");
        }
    } else {
        $json = array("status" => 0, "msg" => "user not found");
    }
} else {
    $json = array("status" => 0, "msg" => "post not found");
}


@mysqli_close($conn);

/* Output header */
header('Content-type: application/json');
echo json_encode($json);
?>
